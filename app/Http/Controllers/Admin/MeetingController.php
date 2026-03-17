<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\MeetingNotification;
use App\Models\ScheduleMeeting;
use App\Notifications\SystemNotification;
use App\Services\GoogleCalender;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ScheduleMeeting::latest()->get();
            return datatables()->of($data)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return $row->user->first_name . ' ' . $row->user->last_name;
                })
                ->editColumn('user_email', function ($row) {
                    return $row->user->email;
                })
                ->addColumn('meeting_date', function ($row) {
                    return \Carbon\Carbon::parse($row->meeting_date)->format('F j, Y');
                })
                ->addColumn('meeting_time', function ($row) {
                    return \Carbon\Carbon::parse($row->meeting_time)->format('g:i A');
                })
                ->addColumn('status', function ($row) {
                    return meetingStatus($row->status);
                })
                ->addColumn('action', function ($row) {
                    if ($row->status == 'verified') {
                        $edit = '<a href="' . route('admin.meeting.show', $row->id) . '" class="btn rounded-pill btn-icon btn-outline-primary me-2"><i class="bx bxs-show"></i></a>';
                        return $edit;
                    } else {
                        $edit = '<a href="' . route('admin.schedule.meetings.edit', $row->id) . '" class="btn rounded-pill btn-icon btn-outline-primary me-2"><i class="bx bxs-edit"></i></a>';
                        return $edit;
                    }
                })
                ->rawColumns(['action', 'user_name', 'user_email', 'status'])
                ->make(true);
        }

        return view('admin.meeting.index');
    }

    public function edit($id)
    {
        $meeting = ScheduleMeeting::findOrFail($id);
        return view('admin.meeting.edit', compact('meeting'));
    }

    public function events(Request $request)
    {
        $events = GoogleCalender::getCalendarEvents(
            getValuesByKey('google_refresh_token'),
            getValuesByKey('google_calendar_id') ?? 'primary',
            $request->input('timeMin')
        );

        return response()->json($events);
    }

    public function show($id)
    {
        $meeting = ScheduleMeeting::with('user')->findOrFail($id);
        return view('admin.meeting.show', compact('meeting'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'status' => 'required|in:approved,rejected,rescheduled,verified',
            'notes'  => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $meeting = ScheduleMeeting::with('user')->findOrFail($id);
            $admin   = Auth::user();

            // -----------------------------
            // APPROVED
            // -----------------------------
            if ($request->status === 'approved') {

                $timezone = config('app.timezone');
                $start = Carbon::parse(
                    $meeting->meeting_date . ' ' . $meeting->meeting_time
                )->toIso8601String();

                $end = Carbon::parse(
                    $meeting->meeting_date . ' ' . $meeting->meeting_time
                )->addMinutes($meeting->duration ?? 30)
                    ->toIso8601String();

                $calendarId = getValuesByKey('google_calendar_id') ?? 'primary';

                $event = GoogleCalender::createMeeting(
                    getValuesByKey('google_refresh_token'),
                    [
                        'title'       => 'Meeting with ' . $meeting->user->first_name,
                        'description' => $meeting->agenda ?? 'Schedule meeting',
                        'start'       => $start,
                        'end'         => $end,
                        'timezone'    => config('app.timezone'),
                        'attendees'   => array_filter([
                            $meeting->user->email,
                            $admin->email,
                        ]),
                    ],
                    $calendarId
                );

                if (empty($event)) {
                    return back()->with('error', $event['message'] ?? 'Failed to create Google Calendar event.');
                }

                // Save Google info
                $meeting->google_event_id  = $event['id'] ?? null;
                $meeting->google_meet_link = $event['conferenceData']['entryPoints'][0]['uri'] ?? null;
                $meeting->calendar_link    = $event['htmlLink'] ?? null;
                $meeting->calender_meeting_status  = 'approved';
                $meeting->status  = 'approved';
                $meeting->admin_notes  = null;
            }

            // -----------------------------
            // REJECTED
            // -----------------------------
            if ($request->status == 'rejected') {
                $meeting->status       = 'rejected';
                $meeting->admin_notes  = $request->admin_notes;
            }

            // -----------------------------
            // RESCHEDULED
            // -----------------------------
            if ($request->status == 'rescheduled') {
                $meeting->status      = 'rescheduled';
                $meeting->admin_notes = $request->admin_notes;
            }

            // -----------------------------
            // VERIFIED
            // -----------------------------
            if ($request->status == 'verified') {
                $meeting->status      = 'verified';
                $meeting->admin_notes = null;
            }

            $meeting->save();


            // Notify user
            if ($meeting->user?->email) {
                Mail::to($meeting->user->email)
                    ->send(new MeetingNotification($meeting));
            }

            DB::commit();

            $status = $request->status;

            // Format meeting date & time
            $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y');
            $meetingTime = \Carbon\Carbon::parse($meeting->meeting_time)->format('h:i A');

            // Build message based on status
            $message = match ($status) {
                'approved' => "Your meeting has been approved on {$meetingDate} at {$meetingTime}.",
                'rejected' => "Your meeting request was rejected." .
                    ($meeting->admin_notes ? " Reason: {$meeting->admin_notes}" : ''),
                'rescheduled' => "Your meeting has been rescheduled to {$meetingDate} at {$meetingTime}." .
                    ($meeting->admin_notes ? " Note: {$meeting->admin_notes}" : ''),
                'verified' => "Your meeting has been verified.",
                default => "Your meeting status has been updated."
            };

            // Icon based on status
            $icon = match ($status) {
                'approved' => 'check-circle',
                'rejected' => 'x-circle',
                'rescheduled' => 'refresh-cw',
                'verified' => 'shield-check',
                default => 'calendar'
            };

            // Notification payload
            $notificationData = [
                'type'  => 'meeting_status_update',
                'title' => 'Meeting Status Updated',
                'message' => $message,
                'icon' => $icon,

                'url' => route('user.scheduleMeetingForm', ['order_id' => encrypt($meeting->order_id)]) ?? null, // optional

                'extra' => [
                    'meeting_id'   => $meeting->id,
                    'status'       => $status,
                    'meeting_date' => $meeting->meeting_date,
                    'meeting_time' => $meeting->meeting_time,
                    'meeting_link' => $meeting->google_meet_link,
                ],
            ];

            if ($meeting->user) {
                $meeting->user->notify(new SystemNotification($notificationData));

                Log::info('Meeting status notification sent', [
                    'user_id' => $meeting->user->id,
                    'meeting_id' => $meeting->id,
                    'status' => $status
                ]);
            }

            return redirect()
                ->back()
                // ->route('admin.schedule.meetings.index')
                ->with('success', 'Meeting updated successfully.');
        } catch (\Exception $e) {
            dd($e->getMessage());

            Log::info('Meeting Update Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            DB::rollBack();
            report($e); //  log instead of dd()

            return back()->with('error', $e->getMessage());
        }
    }
}
