<?php

namespace App\Console\Commands;

use App\Models\ScheduleMeeting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendMeetingReminderJob;

class SendMeetingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-meeting-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        Log::info('Running SendMeetingReminders command (date-based)');

        $today = now(config('app.timezone'))->toDateString();

        Log::info('Today date: ' . $today);

        $meetings = ScheduleMeeting::whereNull('reminder_sent_at')
            ->whereDate('meeting_date', $today)
            ->get();

        Log::info('Meetings fetched count: ' . $meetings->count());

        if ($meetings->isEmpty()) {
            Log::warning('No meetings found for today');
        }

        foreach ($meetings as $meeting) {

            Log::info('Sending reminder for today meeting', [
                'meeting_id'   => $meeting->id,
                'meeting_date' => $meeting->meeting_date,
            ]);

            SendMeetingReminderJob::dispatch($meeting);

            $meeting->update([
                'reminder_sent_at' => now(),
            ]);

            Log::info('Reminder sent and marked for meeting ID: ' . $meeting->id);
        }

        return Command::SUCCESS;
    }
}
