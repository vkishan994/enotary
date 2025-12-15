<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['name'];

    public function notaryServiceTypes()
    {
        return $this->belongsToMany(NotaryServiceType::class, 'document_notary_service_type');
    }
}

