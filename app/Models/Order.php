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

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function veriffData()
    {
        return $this->hasOne(VeriffData::class, 'order_id', 'id');
    }

    public function scheduleMeeting()
    {
        return $this->hasOne(ScheduleMeeting::class, 'order_id', 'id');
    }

    public function notaryServiceType()
    {
        return $this->belongsTo(NotaryServiceType::class);
    }

    public function verifyDocuments()
    {
        return $this->hasMany(VerifyDocument::class);
    }

    public function getNotaryServiceType()
    {
        return $this->belongsTo(NotaryServiceType::class, 'notary_service_type_id', 'id');
    }

    public function getAllDocsVerifiedAttribute()
    {
        $requiredUploadDocs = $this->document
            ? $this->document->uploadDocuments
            : collect();

        $verifyDocs = $this->verifyDocuments;

        return
            $requiredUploadDocs->count() > 0 &&
            $verifyDocs->count() === $requiredUploadDocs->count() &&
            $verifyDocs->every(function ($doc) {

                return $doc->status === 'verified'
                    && $doc->verify_document_items->isNotEmpty()
                    && $doc->verify_document_items->every(
                        fn($item) => $item->status === 'verified'
                    );
            });
    }

    public function reviewedDocument()
    {
        return $this->hasOne(VerifyDocument::class)
            ->whereIn('status', ['approved', 'rejected', 'verified']);
    }
}
