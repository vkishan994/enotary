<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifyDocument extends Model
{

    protected $fillable = ['admin_id', 'user_id', 'order_id', 'document_id', 'upload_documents_id', 'status', 'note'];

    public function verify_document_items()
    {
        return $this->hasMany(VerifyDocumentItems::class, 'verify_document_id');
    }

    public function getdocumentdetail()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function uploadedDocument()
    {
        return $this->belongsTo(UploadDocument::class, 'upload_documents_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
