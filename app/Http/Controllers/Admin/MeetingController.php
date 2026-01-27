<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ScheduleMeeting;
use App\Services\GoogleCalender;
use App\Mail\MeetingNotification;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
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

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,rescheduled',
            'notes'  => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $meeting = ScheduleMeeting::with('user')->findOrFail($id);

            $user = Auth::user();

            if ($request->status === 'approved') {

                $start = Carbon::parse(
                    $request->meeting_date . ' ' . $request->meeting_time
                )->toIso8601String();

                $end = Carbon::parse(
                    $request->meeting_date . ' ' . $request->meeting_time
                )->addMinutes(30)->toIso8601String();

                $calendarId = getValuesByKey('google_calendar_id') ?? 'primary';

                $event = GoogleCalender::createEvent(
                    "Meeting with {$user->first_name}",
                    $request->notes ?? 'Scheduled meeting',
                    $start,
                    $end,
                    [$user->email],
                    $calendarId
                );

                if (!$event) {
                    return back()->with('error', 'Google Calendar event creation failed.');
                }

                dd($event);

                // Save meeting info
                // $meeting->update([
                //     'google_event_id' => $event->getId(),
                //     'google_meet_link' => $event->getHangoutLink(),
                //     'calendar_link' => $event->getHtmlLink(),
                // ]);

                return back()->with('success', 'Meeting scheduled successfully!');
            }

            // Update meeting fields safely
            $meeting->status = $request->status;

            if ($request->status === 'rejected') {
                $meeting->admin_notes = $request->admin_notes;
            }

            $meeting->save();

            // Send email to user based on status
            if ($meeting->user && $meeting->user->email) {
                Mail::to($meeting->user->email)
                    ->send(new MeetingNotification($meeting));
            }

            DB::commit();

            return redirect()
                ->route('admin.schedule.meetings.index')
                ->with('success', 'Meeting updated and user notified successfully.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }
}
