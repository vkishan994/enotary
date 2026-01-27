<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Models\ScheduleMeeting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScheduleMeetingController extends Controller
{
    public function scheduleMeetingForm($order_id)
    {
        $scheduledMeeting = ScheduleMeeting::where('order_id', decrypt($order_id))
            ->where('user_id', Auth::id())
            ->first();

        return view('front.user.meeting.schedule-meeting', compact('order_id', 'scheduledMeeting'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate form data
            $data = $request->validate([
                'meeting_date' => 'required|date|after_or_equal:today',
                'meeting_time' => 'required',
                'notes'        => 'nullable|string',
                'order_id'     => 'required',
            ]);

            // Store meeting in database
            $meeting = ScheduleMeeting::updateOrCreate(
                [
                    'user_id'  => Auth::id(),
                    'order_id' => decrypt($data['order_id']),
                ],
                [
                    'meeting_date' => $data['meeting_date'],
                    'meeting_time' => $data['meeting_time'],
                    'notes'        => $data['notes'] ?? null,
                    'status'       => 'pending', // reset status to pending on update
                ]
            );

            // Optional: create Google Calendar event
            // $this->createGoogleCalendarEvent($meeting);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Meeting Request Send To Admin SuccessFully!');
        } catch (\Throwable $e) {

            DB::rollBack();

            // Log error for debugging
            Log::error('Meeting scheduling failed', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while scheduling the meeting. Please try again.');
        }
    }
}
