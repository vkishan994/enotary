<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadDocument extends Model
{
    protected $fillable = [
        'name',
    ];

    public function documents()
    {
        // UploadDocument belongs to many Documents (inverse)
        return $this->belongsToMany(Document::class, 'required_upload_documents', 'upload_documents_id', 'document_id');
    }
}
