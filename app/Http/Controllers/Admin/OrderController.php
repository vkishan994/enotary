<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\DocumentVerificationMail;
use App\Models\Order;
use App\Models\VerifyDocument;
use App\Models\VerifyDocumentItems;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::with(['user', 'document', 'notaryServiceType'])->latest()->get();

            return Datatables::of($data)
                ->addColumn('id',  fn($row) => $row->id)

                // ->addIndexColumn()
                ->addColumn('user_name', fn($row) => $row->user->first_name . ' ' . $row->user->last_name ?? 'N/A')
                ->addColumn('user_email', fn($row) => $row->user->email ?? 'N/A')
                ->addColumn('document', fn($row) => $row->document->name ?? 'N/A')
                ->addColumn('service_type', fn($row) => $row->notaryServiceType->name ?? 'N/A')
                ->addColumn('amount', fn($row) => '£' . number_format($row->amount, 2))
                ->addColumn('status', function ($row) {
                    return paymentStatus($row->payment_status);
                })

                ->addColumn('upload_document_status', function ($row) {
                    return documentUploadStatus($row->upload_document_status);
                })

                ->addColumn('action', function ($row) {
                    if ($row->upload_document_status === 'submitted') {
                        $edit = '<a href="' . route('admin.orders.detail', $row['id']) . '" class="btn rounded-pill btn-icon btn-outline-primary me-2"><i class="bx bxs-edit"></i></a>';
                        $edit .= '<a href="' . route('admin.orders.show', $row['id']) . '" class="btn rounded-pill btn-icon btn-outline-primary me-2"><i class="bx bxs-show"></i></a>';
                        return $edit;
                    } elseif ($row->upload_document_status === 'verified') {
                        $show = '<a href="' . route('admin.orders.show', $row['id']) . '" class="btn rounded-pill btn-icon btn-outline-primary me-2"><i class="bx bxs-show"></i></a>';
                        return $show;
                    } else {
                        return '';
                    }
                })
                ->addColumn('date', fn($row) => $row->created_at->format('d M Y, H:i'))
                ->rawColumns(['status', 'upload_document_status', 'action'])
                ->make(true);
        }

        return view('admin.orders.index');
    }

    public function orderDetial($id)
    {
        $order = Order::with(['user', 'document', 'notaryServiceType'])->findOrFail($id);
        $uploadedDocuments = VerifyDocument::with('verify_document_items')->where('order_id', $id)->get();
        return view('admin.orders.order_detial', compact('order', 'uploadedDocuments'));
    }

    public function orderShow($id)
    {
        $order = Order::with(['user', 'document', 'notaryServiceType'])->findOrFail($id);
        $uploadedDocuments = VerifyDocument::with('verify_document_items')->where('order_id', $id)->get();
        return view('admin.orders.order_show', compact('order', 'uploadedDocuments'));
    }


    public function changeDocumentStatus($id, Request $request)
    {
        try {
            // dd($request->all());
            DB::beginTransaction();

            $admin = Auth::guard('admin')->user();

            $document = VerifyDocument::findOrFail($id);
            $document->status = $request->status;
            $document->note = $request->status === 'rejected' ? $request->rejection_note : null;
            $document->admin_id = $admin->id;
            $document->save();

            $document_item = VerifyDocumentItems::where('verify_document_id', $id)->update(['status' => $request->status, 'admin_id' => $admin->id]);

            $order = Order::findOrFail($document->order_id);

            // Total documents required for this order
            $totalDocuments = $order->verifyDocuments()->count();

            // Total verified documents
            $verifiedDocuments = $order->verifyDocuments()
                ->where('status', 'verified')
                ->count();

            // If all documents are verified
            if ($totalDocuments > 0 && $totalDocuments === $verifiedDocuments) {
                $order->upload_document_status = 'verified';
                $order->save();
            }

            Mail::to($order->user->email)->send(
                new DocumentVerificationMail(
                    $order->user,
                    $document,
                    $request->status,
                    $request->rejection_note ?? null
                )
            );

            DB::commit();

            // If AJAX request, return JSON success
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Record updated successfully.'
                ]);
            }

            // For normal form submit, redirect back with success message
            return redirect()->back()->with('success', 'Document status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating document status: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update document status. Please try again.'
                ], 500);
            }

            return redirect()->back()->withErrors('Failed to update document status. Please try again.');
        }
    }
}
