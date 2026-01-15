<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\UploadDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use DataTables;


class UploadDocumentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = UploadDocument::get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', fn($row) => $row->name)
                ->addColumn('action', function ($row) {
                    $edit = '<a href="' . route('upload-documents.edit', $row['id']) . '" class="btn rounded-pill btn-icon btn-outline-primary me-2"><i class="bx bxs-edit"></i></a>';
                    $delete = '<a href="#" data-url="' . route('upload-documents.destroy', encrypt($row['id'])) . '" class="btn rounded-pill btn-icon btn-outline-danger item-delete"><i class="bx bxs-trash-alt"></i></a>';
                    return $edit . $delete;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.upload_documents.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.upload_documents.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Store data
            UploadDocument::create([
                'name' => $validated['name'],
            ]);

            DB::commit();

            return redirect()
                ->route('upload-documents.index')
                ->with('success', 'Saved successfully');
        } catch (\Exception $e) {
            // dd($e->getMessage());
            DB::rollBack();

            Log::error('Upload document creation failed: ' . $e->getMessage());

            return redirect()
                ->route('upload-documents.create')
                ->with('error', 'An error occurred while saving. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $record = UploadDocument::findOrFail($id);
        return view('admin.upload_documents.form', compact('record'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $record = UploadDocument::findOrFail($id);

            // Update record
            $record->update([
                'name' => $validated['name'],
            ]);

            DB::commit();

            return redirect()
                ->route('upload-documents.index')
                ->with('success', 'Updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Upload documents update failed: ' . $e->getMessage());

            return redirect()
                ->route('upload-documents.edit', $id)
                ->with('error', 'An error occurred while updating. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $recordId = decrypt($id);
        $record = UploadDocument::find($recordId);
        if ($record) {
            $record->delete();
            return response()->json(['status' => 'success', 'table' => 'upload-documentsTable']);
        }

        return response()->json(['status' => 'error']);
    }
}
