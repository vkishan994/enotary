<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VeriffData extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'end_user_id',
        'vendor_data',
        'status',
        'veriff_decision',
        'veriff_reason',
        'veriff_verified_at',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'veriff_verified_at' => 'datetime',
    ];
}
