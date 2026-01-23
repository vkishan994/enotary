<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerifyDocumentItems extends Model
{
    protected $fillable = ['verify_document_id', 'file_name', 'file_path', 'status', 'note'];

    public function verify_document()
    {
        return $this->belongsTo(VerifyDocument::class, 'verify_document_id');
    }
}
