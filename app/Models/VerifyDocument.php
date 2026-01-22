<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerifyDocument extends Model
{

    protected $fillable = ['user_id', 'order_id', 'document_id', 'upload_documents_id', 'status', 'note'];

    public function verify_document_items()
    {
        return $this->hasMany(VerifyDocumentItems::class, 'verify_document_id');
    }
}
