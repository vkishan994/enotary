<?php

namespace App\Http\Controllers\Front;

use App\Models\Order;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Services\ImageUpload;
use App\Models\UploadDocument;
use App\Models\VerifyDocument;
use Illuminate\Support\Facades\DB;
use App\Models\VerifyDocumentItems;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UploadDocumentController extends Controller
{
    public function documentList(Request $request, $id)
    {
        $order_id = decrypt($id);
        $order = Order::with('document')->findOrFail($order_id);

        // Fetch required upload documents
        $uploadDocuments = $order->document
            ? $order->document->uploadDocuments // This returns UploadDocument collection
            : collect();

        $upload_document_ids = $uploadDocuments->pluck('id');

        $uploadedDocs = VerifyDocument::whereIn('upload_documents_id', $upload_document_ids)
            ->where('user_id', Auth::id())
            ->where('order_id', $order_id)
            ->withCount('verify_document_items')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', 'pending');
            })
            ->get();

        /**
         * All required documents uploaded IF:
         * 1. Count matches
         * 2. Each has at least one uploaded item
         */
        $allUploaded =
            $uploadedDocs->count() === $upload_document_ids->count() &&
            $uploadedDocs->every(fn($doc) => $doc->verify_document_items_count > 0);

        $alreadySubmitted = VerifyDocument::whereIn('upload_documents_id', $upload_document_ids)
            ->where('user_id', Auth::id())
            ->where('order_id', $order_id)
            ->where('status', 'submitted')
            ->exists();

        return view('front.user.order.upload_document.documents_list', compact('uploadDocuments', 'order_id', 'allUploaded', 'alreadySubmitted'));
    }

    public function uploadDocument($order_id, $document_id, $upload_document_id)
    {
        // dd(decrypt($document_id),$order_id,$upload_document_id);
        $uploadDocument = UploadDocument::where('id', decrypt($upload_document_id))->first();
        $userUploadedDocuments = VerifyDocument::where('user_id', Auth::user()->id)
            ->where('order_id', decrypt($order_id))
            ->where('document_id', decrypt($document_id))
            ->where('upload_documents_id', decrypt($upload_document_id))
            ->with('verify_document_items')
            ->first();


        return view('front.user.order.upload_document.upload_documents', compact('uploadDocument', 'order_id', 'document_id', 'userUploadedDocuments'));
    }

    public function storeUploadDocument(Request $request, $order_id, $document_id, $upload_document_id)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        DB::beginTransaction();

        try {
            $file = $request->file('file');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $originalName = preg_replace('/[^A-Za-z0-9_-]/', '_', $originalName);
            $extension = $file->getClientOriginalExtension();
            $timestamp = now()->format('Ymd_His');
            $orderId = decrypt($request->order_id);
            $fileName = $originalName . '_' . $timestamp . '_' . $orderId . '.' . $extension;
            $folderPath = 'verify-documents';

            // Upload the file using your helper
            $filePath = ImageUpload::upload(
                $request->file('file'),
                $folderPath,
                $fileName
            );

            // Find or create verify document record
            $verifyDocument = VerifyDocument::firstOrCreate(
                [
                    'user_id'             => Auth::id(),
                    'order_id'            => $orderId,
                    'document_id'         => decrypt($request->document_id),
                    'upload_documents_id' => decrypt($request->upload_document_id),
                ],
                [
                    'status' => 'pending',
                ]
            );

            // Create new verify document item record
            $verifyDocumentItem = VerifyDocumentItems::create([
                'verify_document_id' => $verifyDocument->id,
                'file_name'          => $fileName,
                'file_path'          => $filePath,
                'status'             => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'success'      => true,
                'file_id'      => $verifyDocumentItem->id,
                'file_name'    => $fileName,
                'download_url' => asset('storage/' . $filePath),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error or handle it as needed
            Log::error('Upload Document Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading the document. Please try again.',
            ], 500);
        }
    }
    public function deleteUploadDocument(Request $request)
    {
        $request->validate([
            'file_id' => 'required'
        ]);

        DB::beginTransaction();

        try {
            // Find file record
            $item = VerifyDocumentItems::find($request->file_id);

            // If not found, try decrypting
            if (empty($item)) {
                $id = decrypt($request->file_id);
                $item = VerifyDocumentItems::find($id);
            }

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Optional: prevent deletion if already submitted/uploaded
            if (in_array($item->status, ['uploaded', 'submitted'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete a file that has been submitted.'
                ], 403);
            }

            // Delete file from storage only if exists
            ImageUpload::delete($item->file_path, 'public');

            // Delete DB record
            $item->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'file_id' => $item->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Delete Upload Document Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the file. Please try again.'
            ], 500);
        }
    }

    public function submitDocumentForVerification(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $order_id = decrypt($request->order_id);

            // Fetch the order
            $order = Order::with('document')->findOrFail($order_id);

            // Fetch required upload documents
            $uploadDocuments = $order->document
                ? $order->document->uploadDocuments
                : collect();

            $upload_document_ids = $uploadDocuments->pluck('id');

            $uploadedDocs = VerifyDocument::whereIn('upload_documents_id', $upload_document_ids)
                ->where('user_id', Auth::id())
                ->where('order_id', $order_id)
                ->withCount('verify_document_items')
                ->get();

            /**
             * All required documents uploaded IF:
             * 1. Count matches
             * 2. Each has at least one uploaded item
             */
            $allUploaded =
                $uploadedDocs->count() === $upload_document_ids->count() &&
                $uploadedDocs->every(fn($doc) => $doc->verify_document_items_count > 0);

            if (!$allUploaded) {
                return redirect()->back()->with('error', 'Please upload all required documents before submitting for verification.');
            }

            // Update status of all uploaded documents to 'submitted'
            foreach ($uploadedDocs as $doc) {
                $doc->status = 'submitted';
                $doc->save();

                $doc->verify_document_items()->update(['status' => 'submitted']);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Documents submitted for verification successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Submit Documents for Verification Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while submitting documents. Please try again.');
        }
    }
}
