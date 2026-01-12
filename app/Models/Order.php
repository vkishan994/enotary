<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'document_id',
        'notary_service_type_id',
        'amount',
        'currency',
        'payment_status',
        'stripe_payment_intent_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function notaryServiceType()
    {
        return $this->belongsTo(NotaryServiceType::class);
    }
}
