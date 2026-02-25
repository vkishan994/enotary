<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'document_id',
        'notary_service_type_id',
        'amount',
        'currency',
        'payment_status',
        'stripe_payment_intent_id',
        'invoice_number',
        'invoice_generated_at',
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

    public function hasUserUploadedAllDocuments(int $userId): bool
    {
        // Required upload documents for this order
        // Fetch required upload documents
        $uploadDocuments = $this->document
            ? $this->document->uploadDocuments // This returns UploadDocument collection
            : collect();

        $upload_document_ids = $uploadDocuments->pluck('id');

        $uploadedDocs = VerifyDocument::whereIn('upload_documents_id', $upload_document_ids)
            ->where('user_id', $userId)
            ->where('order_id', $this->id)
            ->withCount('verify_document_items')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereIn('status', [
                        'approved',
                        'rejected',
                        'verified',
                        'submitted'
                    ]);
            })
            ->get();

        /**
         * All required documents uploaded IF:
         * 1. Count matches
         * 2. Each has at least one uploaded item
         */
        $allUploaded =
            $uploadedDocs->count() === $upload_document_ids->count() &&
            $uploadedDocs->every(fn($doc) => $doc->verify_document_items_count > 0);


        return $allUploaded;
    }

    public static function generateInvoiceNumber()
    {
        return 'INV-' . date('Y') . '-' . str_pad(self::count() + 1, 5, '0', STR_PAD_LEFT);
    }
}
