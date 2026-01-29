<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ScheduleMeeting;
use App\Services\GoogleCalender;
use App\Mail\MeetingNotification;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Auth;
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
                    $edit = '<a href="' . route('admin.schedule.meetings.edit', $row->id) . '" class="btn rounded-pill btn-icon btn-outline-primary me-2"><i class="bx bxs-edit"></i></a>';
                    return $edit;
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

    public function update(Request $request, $id)
    {
   
        $request->validate([
            'status' => 'required|in:approved,rejected,rescheduled',
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

            $meeting->save();


            // Notify user
            if ($meeting->user?->email) {
                Mail::to($meeting->user->email)
                    ->send(new MeetingNotification($meeting));
            }

            DB::commit();

            return redirect()
                ->route('admin.schedule.meetings.index')
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
