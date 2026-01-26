<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Models\UploadDocument;
use App\Models\NotaryServiceType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentRequest;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Document::with('notaryServiceTypes')->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', fn($row) => $row->name)
                ->addColumn('notary_service_types', fn($row) => $row->notaryServiceTypes->pluck('name')->join(', '))
                ->addColumn('action', function ($row) {
                    $edit = '<a href="' . route('documents.edit', $row['id']) . '" class="btn rounded-pill btn-icon btn-outline-primary me-2"><i class="bx bxs-edit"></i></a>';
                    $delete = '<a href="#" data-url="' . route('documents.destroy', encrypt($row['id'])) . '" class="btn rounded-pill btn-icon btn-outline-danger item-delete"><i class="bx bxs-trash-alt"></i></a>';
                    return $edit . $delete;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.documents.index');
    }

    public function create()
    {
        $notaryServiceTypes = NotaryServiceType::all();
        $uploadDocuments = UploadDocument::all();
        return view('admin.documents.form', compact('notaryServiceTypes', 'uploadDocuments'));
    }

    public function store(DocumentRequest $request)
    {
        DB::beginTransaction();

        try {
            $document = Document::create($request->validated());
            $document->notaryServiceTypes()->sync($request->input('notary_service_types', []));
            DB::commit();

            return redirect()->route('documents.index')->with('success', 'Saved Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('documents creation failed: ' . $e->getMessage());

            return redirect()->route('documents.create')
                ->with('error', 'An error occurred while saving. Please try again.')
                ->withInput();
        }
    }

    public function edit(string $id)
    {
        $record = Document::with('notaryServiceTypes')->findOrFail($id);
        $notaryServiceTypes = NotaryServiceType::all();
        $uploadDocuments = UploadDocument::all();

        return view('admin.documents.form', compact('record', 'notaryServiceTypes', 'uploadDocuments'));
    }

    public function update(DocumentRequest $request, string $id)
    {
        DB::beginTransaction();

        try {
            $record = Document::findOrFail($id);
            $record->update($request->validated());
            $record->notaryServiceTypes()->sync($request->input('notary_service_types', []));
            $record->uploadDocuments()->sync($request->input('upload_documents', []));
            DB::commit();

            return redirect()->route('documents.index')->with('success', 'Saved Successfully');
        } catch (\Exception $e) {
            // dd($e->getMessage());
            DB::rollBack();
            Log::error('documents update failed: ' . $e->getMessage());

            return redirect()->route('documents.edit', $id)
                ->with('error', 'An error occurred while updating. Please try again.')
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        $recordId = decrypt($id);
        $record = Document::find($recordId);
        if ($record) {
            $record->delete();
            return response()->json(['status' => 'success', 'table' => 'documentsTable']);
        }

        return response()->json(['status' => 'error']);
    }
}
