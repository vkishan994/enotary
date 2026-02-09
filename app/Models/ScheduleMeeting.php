<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleMeeting extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'meeting_date',
        'meeting_time',
        'notes',
        'google_event_id',
        'admin_notes',
        'status',
        'reminder_sent_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
