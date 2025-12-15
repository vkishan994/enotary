<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotaryServiceTypeRequest;
use App\Models\NotaryServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DataTables;

class NotaryServiceTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = NotaryServiceType::with([])->get();

            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('name', fn ($row) => $row->name)
                ->addColumn('action', function ($row) {
                    $edit = '<a href="' . route('notary-service-types.edit', $row['id']) . '" class="btn rounded-pill btn-icon btn-outline-primary me-2"><i class="bx bxs-edit"></i></a>';
                    $delete = '<a href="#" data-url="' . route('notary-service-types.destroy', encrypt($row['id'])) . '" class="btn rounded-pill btn-icon btn-outline-danger item-delete"><i class="bx bxs-trash-alt"></i></a>';

                    return $edit . $delete;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.notary-service-types.index');
    }

    public function create()
    {
        return view('admin.notary-service-types.form', [
            'title' => 'Create',
        ]);
    }

    public function store(NotaryServiceTypeRequest $request)
    {
        DB::beginTransaction();

        try {
            $model = NotaryServiceType::create($request->validated());

            DB::commit();

            return redirect()->route('notary-service-types.index')->with('success', 'Saved Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('notary-service-types creation failed: ' . $e->getMessage());

            return redirect()->route('notary-service-types.create')
                ->with('error', 'An error occurred while saving. Please try again.')
                ->withInput();
        }
    }

    public function edit(string $id)
    {
        $record = NotaryServiceType::with([])->findOrFail($id);
        return view('admin.notary-service-types.form', [
            'record' => $record,
            'title' => 'Edit',
        ]);
    }

    public function update(NotaryServiceTypeRequest $request, string $id)
    {
        DB::beginTransaction();

        try {
            $record = NotaryServiceType::findOrFail($id);
            $record->update($request->validated());

            DB::commit();

            return redirect()->route('notary-service-types.index')->with('success', 'Saved Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('notary-service-types update failed: ' . $e->getMessage());

            return redirect()->route('notary-service-types.edit', $id)
                ->with('error', 'An error occurred while updating. Please try again.')
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        $recordId = decrypt($id);
        $record = NotaryServiceType::find($recordId);
        if ($record) {
            $record->delete();
            return response()->json(['status' => 'success', 'table' => 'notary-service-typesTable']);
        }

        return response()->json(['status' => 'error']);
    }
}
