<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['name', 'price'];

    public function notaryServiceTypes()
    {
        return $this->belongsToMany(NotaryServiceType::class, 'document_notary_service_type');
    }

    public function uploadDocuments()
    {
        // Document has many required UploadDocuments through pivot table
        return $this->belongsToMany(UploadDocument::class, 'required_upload_documents', 'document_id', 'upload_documents_id');
    }

    public function verifyDocuments()
    {
        return $this->hasMany(VerifyDocument::class, 'document_id');
    }
}
