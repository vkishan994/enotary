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
        return $this->belongsToMany(UploadDocument::class, 'required_upload_documents','upload_documents_id','document_id');
    }
}
