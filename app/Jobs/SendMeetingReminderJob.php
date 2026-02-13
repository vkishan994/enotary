<?php

namespace App\Jobs;

use Throwable;
use App\Models\Admin;
use App\Models\ScheduleMeeting;
use Illuminate\Support\Facades\Log;
use App\Notifications\SystemNotification;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\MeetingReminderNotification;

class SendMeetingReminderJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public $meeting;

    public function __construct(ScheduleMeeting $meeting)
    {
        $this->meeting = $meeting;
    }

    public function handle()
    {
        Log::info('SendMeetingReminderJob started', [
            'meeting_id' => $this->meeting->id ?? null,
        ]);

        try {
            // Common notification payload
            $notificationData = [
                'type'  => 'meeting_reminder',
                'title' => 'Today’s Scheduled Meeting',

                'message' => 'You have a meeting scheduled today with '
                    . $this->meeting->user->first_name . ' '
                    . $this->meeting->user->last_name
                    . ' at '
                    . \Carbon\Carbon::parse($this->meeting->meeting_time)->format('h:i A')
                    . ' on '
                    . \Carbon\Carbon::parse($this->meeting->meeting_date)->format('d M Y')
                    . '.',

                'icon' => 'calendar',

                'extra' => [
                    'meeting_id'   => $this->meeting->id,
                    'meeting_date' => $this->meeting->meeting_date,
                    'meeting_time' => $this->meeting->meeting_time,
                ],
            ];

            /** ---------------- USER ---------------- */
            if ($this->meeting->user) {
                Log::info('Sending meeting reminder to user', [
                    'user_id' => $this->meeting->user->id,
                ]);

                $this->meeting->user->notify(
                    new SystemNotification($notificationData)
                );
            } else {
                Log::warning('Meeting user not found', [
                    'meeting_id' => $this->meeting->id,
                ]);
            }

            /** ---------------- ADMIN ---------------- */
            $admin = Admin::first();

            if ($admin) {
                Log::info('Sending meeting reminder to admin', [
                    'admin_id' => $admin->id,
                ]);

                $admin->notify(
                    new SystemNotification($notificationData)
                );
            } else {
                Log::warning('Admin not found while sending meeting reminder');
            }

            Log::info('SendMeetingReminderJob completed successfully', [
                'meeting_id' => $this->meeting->id,
            ]);
        } catch (Throwable $e) {

            Log::error('SendMeetingReminderJob failed', [
                'meeting_id' => $this->meeting->id ?? null,
                'error'      => $e->getMessage(),
                'file'       => $e->getFile(),
                'line'       => $e->getLine(),
            ]);

            throw $e; // Marks job as failed
        }
    }
}
