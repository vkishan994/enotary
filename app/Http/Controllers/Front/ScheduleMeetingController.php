<?php

namespace App\Http\Controllers\Front;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\ScheduleMeeting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SystemNotification;

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

            // Parse requested start and end time
            $start = Carbon::parse($data['meeting_date'] . ' ' . $data['meeting_time']);
            $durationMinutes = 30; // default duration if not saved somewhere else
            $end = (clone $start)->addMinutes($durationMinutes);

            // Check for conflicting meetings booked by *other users*
            $conflict = ScheduleMeeting::where('meeting_date', $data['meeting_date'])
                ->where('user_id', '!=', Auth::id())
                ->where(function ($query) use ($start, $end) {
                    $query->where(function ($q) use ($start, $end) {
                        $q->whereRaw("TIME(meeting_time) < ?", [$end->format('H:i:s')])
                            ->whereRaw("ADDTIME(meeting_time, SEC_TO_TIME(30 * 60)) > ?", [$start->format('H:i:s')]);
                    });
                })
                ->exists();

            if ($conflict) {
                DB::rollBack();
                return back()->with('error', 'Selected time slot is already booked. Please select another time.');
            }

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
                    'admin_notes'  => null,
                ]
            );

            // Send notification to admin
            $admin = Admin::first();
            if ($admin) {
                $user = Auth::user();
                $admin->notify(new SystemNotification([
                    'type'    => 'meeting_scheduled',
                    'title'   => 'New Meeting Request',
                    'message' => $user->name . ' has requested a meeting on ' . $data['meeting_date'] . ' at ' . $data['meeting_time'],
                    'url'     => route('customers.list', ['user_id' => $user->id, 'order_id' => decrypt($data['order_id'])]),
                    'icon'    => 'calendar',
                    'extra'   => [
                        'meeting_id' => $meeting->id,
                        'user_id'    => $user->id,
                        'user_name'  => $user->name,
                        'meeting_date' => $data['meeting_date'],
                        'meeting_time' => $data['meeting_time'],
                    ],
                ]));
            }

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
