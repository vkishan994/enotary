<?php

namespace App\Http\Controllers\Front;

use App\Models\Order;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UploadDocument;

class UploadDocumentController extends Controller
{
    public function documentList(Request $request, $id)
    {
        $order_id = decrypt($id);
        $order = Order::with('document')->findOrFail($order_id);

        // Fetch required upload documents
        $uploadDocuments = $order->document
            ? $order->document->uploadDocuments
            : collect();

        return view('front.user.order.upload_document.documents_list', compact('uploadDocuments', 'order_id'));
    }

    public function uploadDocument($order_id, $document_id, $upload_document_id)
    {
        $uploadDocument = UploadDocument::where('id', decrypt($upload_document_id))->first();
        return view('front.user.order.upload_document.upload_documents', compact('uploadDocument', 'order_id', 'document_id'));
    }
    public function storeUploadDocument(Request $request,$order_id, $document_id, $upload_document_id) {
        // dd($request->all(),$order_id,$document_id,$upload_document_id);
    }
}
