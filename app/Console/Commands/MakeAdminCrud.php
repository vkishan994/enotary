<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeAdminCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin-crud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate admin CRUD scaffolding (controller, request, views, migration, model, routes)';

    public function __construct(private Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $crudName = $this->ask('Crud name (e.g. Notary Service Type)');
        if (!$crudName) {
            $this->error('Crud name is required.');
            return self::FAILURE;
        }

        $rawFields = $this->ask(
            'Fields (comma separated "name:type", type: string|text|integer|boolean|date|datetime)',
            'name:string'
        );
        $rawBelongsTo = $this->ask(
            'BelongsTo foreign keys (comma separated "user_id:users"), leave blank if none',
            ''
        );
        $rawBelongsToMany = $this->ask(
            'BelongsToMany tables (comma separated table names), leave blank if none',
            ''
        );

        $fields = $this->parseFields($rawFields);
        $belongsTo = $this->parseBelongsTo($rawBelongsTo);
        $belongsToMany = $this->parseBelongsToMany($rawBelongsToMany);

        $modelName = Str::studly($crudName);
        $controllerName = "{$modelName}Controller";
        $requestName = "{$modelName}Request";
        $table = Str::snake(Str::pluralStudly($modelName));
        $viewFolder = Str::kebab(Str::pluralStudly($modelName));

        $this->generateMigration($table, $fields, $belongsTo, $belongsToMany);
        $this->generateModel($modelName, $fields, $belongsTo, $belongsToMany);
        $this->generateRequest($requestName, $fields, $belongsTo, $belongsToMany);
        $this->generateController($controllerName, $modelName, $requestName, $viewFolder, $fields, $belongsTo, $belongsToMany);
        $this->generateViews($viewFolder, $modelName, $fields, $belongsTo, $belongsToMany);
        $this->updateRoutes($viewFolder, $controllerName);

        $this->info('Admin CRUD scaffolding generated. Review files before running migrations.');

        return self::SUCCESS;
    }

    /**
     * @return array<int, array{name:string,type:string}>
     */
    private function parseFields(string $rawFields): array
    {
        $fields = [];
        foreach (array_filter(array_map('trim', explode(',', $rawFields))) as $field) {
            [$name, $type] = array_pad(array_map('trim', explode(':', $field)), 2, 'string');
            if ($name === 'id' || $name === '') {
                continue;
            }
            $fields[] = ['name' => $name, 'type' => $type ?: 'string'];
        }

        return $fields;
    }

    /**
     * @return array<int, array{column:string,table:string}>
     */
    private function parseBelongsTo(string $rawBelongsTo): array
    {
        $items = [];
        foreach (array_filter(array_map('trim', explode(',', $rawBelongsTo))) as $entry) {
            [$column, $table] = array_pad(array_map('trim', explode(':', $entry)), 2, null);
            if ($column && $table) {
                $items[] = ['column' => $column, 'table' => $table];
            }
        }

        return $items;
    }

    /**
     * @return array<int, string>
     */
    private function parseBelongsToMany(string $rawBelongsToMany): array
    {
        return array_filter(array_map('trim', explode(',', $rawBelongsToMany)));
    }

    private function generateMigration(string $table, array $fields, array $belongsTo, array $belongsToMany): void
    {
        $timestamp = now()->format('Y_m_d_His');
        $migrationName = "{$timestamp}_create_{$table}_table.php";
        $path = database_path("migrations/{$migrationName}");

        $fieldLines = [];
        foreach ($fields as $field) {
            $type = $field['type'];
            $name = $field['name'];
            $fieldLines[] = "\$table->{$type}('{$name}')->nullable();";
        }

        foreach ($belongsTo as $relation) {
            $column = $relation['column'];
            $tableName = $relation['table'];
            $fieldLines[] = "\$table->foreignId('{$column}')->constrained('{$tableName}')->cascadeOnDelete();";
        }

        $fieldBody = implode("\n            ", $fieldLines);

        $migration = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table) {
            \$table->id();
            {$fieldBody}
            \$table->timestamps();
        });

PHP;

        if (!empty($belongsToMany)) {
            foreach ($belongsToMany as $relatedTable) {
                $pivot = $this->pivotTableName($table, $relatedTable);
                $parentColumn = Str::singular($table) . '_id';
                $relatedColumn = Str::singular($relatedTable) . '_id';
                $migration .= <<<PHP

        Schema::create('{$pivot}', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('{$parentColumn}')->constrained('{$table}')->cascadeOnDelete();
            \$table->foreignId('{$relatedColumn}')->constrained('{$relatedTable}')->cascadeOnDelete();
            \$table->timestamps();
        });

PHP;
            }
        }

        $migration .= <<<PHP
    }

    public function down(): void
    {
        {$this->generatePivotDrops($table, $belongsToMany)}
        Schema::dropIfExists('{$table}');
    }
};
PHP;

        File::put($path, $migration);
        $this->components->info("Migration created: {$migrationName}");
    }

    private function generatePivotDrops(string $table, array $belongsToMany): string
    {
        $lines = [];
        foreach ($belongsToMany as $relatedTable) {
            $pivot = $this->pivotTableName($table, $relatedTable);
            $lines[] = "Schema::dropIfExists('{$pivot}');";
        }

        return implode("\n        ", $lines);
    }

    private function pivotTableName(string $firstTable, string $secondTable): string
    {
        $tables = [$firstTable, $secondTable];
        sort($tables);

        return implode('_', $tables);
    }

    private function generateModel(string $modelName, array $fields, array $belongsTo, array $belongsToMany): void
    {
        $path = app_path("Models/{$modelName}.php");

        $fillable = array_map(fn ($field) => "'{$field['name']}'", $fields);
        foreach ($belongsTo as $relation) {
            $fillable[] = "'{$relation['column']}'";
        }
        $fillableList = implode(', ', $fillable);

        $relations = [];
        foreach ($belongsTo as $relation) {
            $method = Str::camel(Str::before($relation['column'], '_id'));
            $relatedModel = Str::studly(Str::singular($relation['table']));
            $relations[] = <<<PHP
    public function {$method}()
    {
        return \$this->belongsTo({$relatedModel}::class, '{$relation['column']}');
    }

PHP;
        }

        foreach ($belongsToMany as $relatedTable) {
            $method = Str::camel(Str::pluralStudly(Str::singular($relatedTable)));
            $relatedModel = Str::studly(Str::singular($relatedTable));
            $pivot = $this->pivotTableName(Str::snake(Str::pluralStudly($modelName)), $relatedTable);
            $relations[] = <<<PHP
    public function {$method}()
    {
        return \$this->belongsToMany({$relatedModel}::class, '{$pivot}');
    }

PHP;
        }

        $relationsBlock = implode("\n", $relations);

        $model = <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class {$modelName} extends Model
{
    protected \$fillable = [{$fillableList}];

{$relationsBlock}}
PHP;

        $this->ensureDirectory(dirname($path));
        File::put($path, $model);
        $this->components->info("Model created: {$modelName}");
    }

    private function generateRequest(string $requestName, array $fields, array $belongsTo, array $belongsToMany): void
    {
        $path = app_path("Http/Requests/{$requestName}.php");

        $rules = [];
        foreach ($fields as $field) {
            $rules[] = "'{$field['name']}' => 'required'";
        }
        foreach ($belongsTo as $relation) {
            $rules[] = "'{$relation['column']}' => 'required|exists:{$relation['table']},id'";
        }
        foreach ($belongsToMany as $relatedTable) {
            $rules[] = "'" . Str::camel(Str::plural($relatedTable)) . "' => 'array'";
            $rules[] = "'" . Str::camel(Str::plural($relatedTable)) . ".*' => 'exists:{$relatedTable},id'";
        }
        $rulesBody = implode(",\n            ", $rules);

        $request = <<<PHP
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class {$requestName} extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            {$rulesBody}
        ];
    }
}
PHP;

        $this->ensureDirectory(dirname($path));
        File::put($path, $request);
        $this->components->info("Request created: {$requestName}");
    }

    private function generateController(
        string $controllerName,
        string $modelName,
        string $requestName,
        string $viewFolder,
        array $fields,
        array $belongsTo,
        array $belongsToMany
    ): void {
        $path = app_path("Http/Controllers/Admin/{$controllerName}.php");

        $withRelations = collect($belongsTo)->map(
            fn ($relation) => "'" . Str::camel(Str::before($relation['column'], '_id')) . "'"
        )->merge(
            collect($belongsToMany)->map(fn ($table) => "'" . Str::camel(Str::plural($table)) . "'")
        )->filter()->implode(', ');

        $indexColumns = implode("\n                ", array_map(
            fn ($field) => "->addColumn('{$field['name']}', fn (\$row) => \$row->{$field['name']})",
            $fields
        ));

        $belongsToManyColumns = '';
        foreach ($belongsToMany as $relatedTable) {
            $method = Str::camel(Str::plural($relatedTable));
            $columnName = Str::snake($method);
            $belongsToManyColumns .= "                ->addColumn('{$columnName}', fn (\$row) => \$row->{$method}->pluck('name')->join(', '))\n";
        }

        $relationCollections = [];
        foreach ($belongsTo as $relation) {
            $relationCollections[] = "\$" . Str::camel(Str::plural($relation['table'])) . " = \\App\\Models\\" . Str::studly(Str::singular($relation['table'])) . "::all();";
        }
        foreach ($belongsToMany as $relatedTable) {
            $relationCollections[] = "\$" . Str::camel(Str::plural($relatedTable)) . " = \\App\\Models\\" . Str::studly(Str::singular($relatedTable)) . "::all();";
        }
        $relationsLoad = implode("\n        ", $relationCollections);

        $syncManyToMany = '';
        foreach ($belongsToMany as $relatedTable) {
            $method = Str::camel(Str::plural($relatedTable));
            $syncManyToMany .= "        \$model->{$method}()->sync(\$request->input('{$method}', []));\n";
        }

        $relationLoadBlock = $relationsLoad ? "        {$relationsLoad}\n" : '';
        $relationCompactBlock = $this->viewCompact($relationCollections);

        $controller = <<<PHP
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\\{$requestName};
use App\Models\\{$modelName};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DataTables;

class {$controllerName} extends Controller
{
    public function index(Request \$request)
    {
        if (\$request->ajax()) {
            \$query = {$modelName}::with([{$withRelations}])->get();

            return Datatables::of(\$query)
                ->addIndexColumn()
                {$indexColumns}
{$belongsToManyColumns}                ->addColumn('action', function (\$row) {
                    \$edit = '<a href=\"' . route('{$viewFolder}.edit', \$row['id']) . '\" class=\"btn rounded-pill btn-icon btn-outline-primary me-2\"><i class=\"bx bxs-edit\"></i></a>';
                    \$delete = '<a href=\"#\" data-url=\"' . route('{$viewFolder}.destroy', encrypt(\$row['id'])) . '\" class=\"btn rounded-pill btn-icon btn-outline-danger item-delete\"><i class=\"bx bxs-trash-alt\"></i></a>';

                    return \$edit . \$delete;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.{$viewFolder}.index');
    }

    public function create()
    {
{$relationLoadBlock}        return view('admin.{$viewFolder}.form', [
            'title' => 'Create',
{$relationCompactBlock}        ]);
    }

    public function store({$requestName} \$request)
    {
        DB::beginTransaction();

        try {
            \$model = {$modelName}::create(\$request->validated());
{$syncManyToMany}
            DB::commit();

            return redirect()->route('{$viewFolder}.index')
                ->with('success', $this->successMessage({$modelName}::class, 'added'));
        } catch (\\Exception \$e) {
            DB::rollBack();
            Log::error('{$viewFolder} creation failed: ' . \$e->getMessage());

            return redirect()->route('{$viewFolder}.create')
                ->with('error', 'An error occurred while saving. Please try again.')
                ->withInput();
        }
    }

    public function edit(string \$id)
    {
        \$record = {$modelName}::with([{$withRelations}])->findOrFail(\$id);
{$relationLoadBlock}        return view('admin.{$viewFolder}.form', [
            'record' => \$record,
            'title' => 'Edit',
{$relationCompactBlock}        ]);
    }

    public function update({$requestName} \$request, string \$id)
    {
        DB::beginTransaction();

        try {
            \$record = {$modelName}::findOrFail(\$id);
            \$record->update(\$request->validated());
{$syncManyToMany}
            DB::commit();

            return redirect()->route('{$viewFolder}.index')
                ->with('success', $this->successMessage({$modelName}::class, 'updated'));
        } catch (\\Exception \$e) {
            DB::rollBack();
            Log::error('{$viewFolder} update failed: ' . \$e->getMessage());

            return redirect()->route('{$viewFolder}.edit', \$id)
                ->with('error', 'An error occurred while updating. Please try again.')
                ->withInput();
        }
    }

    public function destroy(string \$id)
    {
        \$recordId = decrypt(\$id);
        \$record = {$modelName}::find(\$recordId);
        if (\$record) {
            \$record->delete();
            return response()->json(['status' => 'success', 'table' => '{$viewFolder}Table']);
        }

        return response()->json(['status' => 'error']);
    }
}
PHP;

        $this->ensureDirectory(dirname($path));
        File::put($path, $controller);
        $this->components->info("Controller created: {$controllerName}");
    }

    private function viewCompact(array $relationCollections): string
    {
        if (empty($relationCollections)) {
            return '';
        }

        $lines = [];
        foreach ($relationCollections as $line) {
            $var = trim(Str::before($line, '='));
            $var = trim($var, '$ ');
            $lines[] = "            '{$var}' => \${$var},\n";
        }

        return implode('', $lines);
    }

    private function generateViews(
        string $viewFolder,
        string $modelName,
        array $fields,
        array $belongsTo,
        array $belongsToMany
    ): void {
        $indexPath = resource_path("views/admin/{$viewFolder}/index.blade.php");
        $formPath = resource_path("views/admin/{$viewFolder}/form.blade.php");

        $folderDir = dirname($indexPath);
        $this->ensureDirectory($folderDir);

        $listColumns = '';
        foreach ($fields as $field) {
            $listColumns .= "                        <th>" . Str::title(str_replace('_', ' ', $field['name'])) . "</th>\n";
        }
        foreach ($belongsToMany as $relatedTable) {
            $listColumns .= "                        <th>" . Str::title(str_replace('_', ' ', $relatedTable)) . "</th>\n";
        }

        $datatableColumns = '';
        $datatableColumns .= "                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },\n";
        foreach ($fields as $field) {
            $datatableColumns .= "                        { data: '{$field['name']}', name: '{$field['name']}' },\n";
        }
        foreach ($belongsToMany as $relatedTable) {
            $column = Str::snake(Str::plural($relatedTable));
            $datatableColumns .= "                        { data: '{$column}', name: '{$column}' },\n";
        }
        $datatableColumns .= "                        { data: 'action', name: 'action', orderable: false, searchable: false },\n";

        $indexView = <<<BLADE
@extends('admin.layouts.common')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h4 class="py-3 mb-4">
                <span class="text-muted fw-light">{$modelName} /</span> List
            </h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('{$viewFolder}.create') }}" class="btn btn-primary">Add {$modelName}</a>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">{$modelName} List</h5>
        <div class="card-body">
            <table class="datatables-ajax table table-bordered" id="{$viewFolder}Table">
                <thead>
                    <tr>
                        <th>#</th>
{$listColumns}                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('admin/assets/js/delete-records.js') }}"></script>

        <script>
            $(document).ready(function() {
                $('#{$viewFolder}Table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('{$viewFolder}.index') }}",
                    columns: [
{$datatableColumns}                    ]
                });
            });
        </script>
    @endpush
@endsection
BLADE;

        File::put($indexPath, $indexView);

        $formFields = '';
        foreach ($fields as $field) {
            $label = Str::title(str_replace('_', ' ', $field['name']));
            $formFields .= <<<BLADE
                        <div class="col-md-4">
                            <label class="form-label" for="{$field['name']}">{$label}</label>
                            <input type="text" name="{$field['name']}" class="form-control"
                                value="{{ old('{$field['name']}', isset(\$record) ? \$record->{$field['name']} : '') }}" id="{$field['name']}"
                                placeholder="Enter {$label}" />
                            @error('{$field['name']}')
                                <div class="invalid-feedback">
                                    <strong>{{ \$message }}</strong>
                                </div>
                            @enderror
                        </div>

BLADE;
        }

        foreach ($belongsTo as $relation) {
            $var = Str::camel(Str::plural($relation['table']));
            $label = Str::title(str_replace('_', ' ', Str::singular($relation['table'])));
            $formFields .= <<<BLADE
                        <div class="col-md-4">
                            <label class="form-label" for="{$relation['column']}">{$label}</label>
                            <select name="{$relation['column']}" id="{$relation['column']}" class="form-select">
                                <option value="">Select {$label}</option>
                                @foreach (\${$var} as \$item)
                                    <option value="{{ \$item->id }}" {{ old('{$relation['column']}', isset(\$record) ? \$record->{$relation['column']} : '') == \$item->id ? 'selected' : '' }}>
                                        {{ \$item->name ?? \$item->id }}
                                    </option>
                                @endforeach
                            </select>
                            @error('{$relation['column']}')
                                <div class="invalid-feedback">
                                    <strong>{{ \$message }}</strong>
                                </div>
                            @enderror
                        </div>

BLADE;
        }

        foreach ($belongsToMany as $relatedTable) {
            $var = Str::camel(Str::plural($relatedTable));
            $label = Str::title(str_replace('_', ' ', $relatedTable));
            $method = Str::camel(Str::plural($relatedTable));
            $formFields .= <<<BLADE
                        <div class="col-md-6">
                            <label class="form-label" for="{$method}">{$label}</label>
                            <select name="{$method}[]" id="{$method}" class="form-select" multiple>
                                @foreach (\${$var} as \$item)
                                    <option value="{{ \$item->id }}"
                                        @if(isset(\$record) && \$record->{$method}->pluck('id')->contains(\$item->id)) selected @endif>
                                        {{ \$item->name ?? \$item->id }}
                                    </option>
                                @endforeach
                            </select>
                            @error('{$method}')
                                <div class="invalid-feedback">
                                    <strong>{{ \$message }}</strong>
                                </div>
                            @enderror
                        </div>

BLADE;
        }

        $formView = <<<BLADE
@extends('admin.layouts.common')
@section('content')
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">{$modelName}/</span> {{ \$title ?? (isset(\$record) ? 'Edit' : 'Create') }}</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{$modelName}</h5>
                </div>
                <div class="card-body">
                    <form
                        action="{{ isset(\$record) ? route('{$viewFolder}.update', \$record->id) : route('{$viewFolder}.store') }}"
                        method="post">
                        @csrf
                        @if (isset(\$record))
                            @method('PUT')
                        @endif

                        <div class="row">
{$formFields}                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
BLADE;

        File::put($formPath, $formView);

        $this->components->info("Views created: resources/views/admin/{$viewFolder}/");
    }

    private function updateRoutes(string $viewFolder, string $controllerName): void
    {
        $routesPath = base_path('routes/web.php');
        $routes = File::get($routesPath);

        $resourceLine = "    Route::resource('{$viewFolder}', \App\Http\Controllers\Admin\\{$controllerName}::class);";
        if (str_contains($routes, $resourceLine)) {
            $this->components->warn('Route already exists, skipping web.php update.');
            return;
        }

        $needle = "Route::group(['prefix' => 'admin','middleware' => ['auth:admin']], function () {";
        if (str_contains($routes, $needle)) {
            $updated = str_replace(
                $needle,
                "{$needle}\n{$resourceLine}\n",
                $routes
            );
            File::put($routesPath, $updated);
            $this->components->info('Route added to web.php');
        } else {
            $this->components->warn('Admin route group not found. Please add the resource route manually:');
            $this->line($resourceLine);
        }
    }

    private function ensureDirectory(string $dir): void
    {
        if (!$this->files->isDirectory($dir)) {
            $this->files->makeDirectory($dir, 0755, true);
        }
    }
}

