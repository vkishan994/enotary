@extends('front.layouts.common')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    @include('front.layouts.dashboard.sidebar')

    <!-- Main content start -->
    <main class="main-content">

        <div class="document-upload document-pending">
            <div class="section-title mb-3">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h4>Schedule A Meeting</h4>
                    </div>

                    <div class="col-6 text-end">
                        <a href="{{ route('user.account-dashboard') }}" class="btn back-btn">Back <a>
                    </div>
                </div>
            </div>

            <x-alert type="success" :message="session('success')" />
            <x-alert type="danger" :message="session('error')" />

            <!-- Schedule Form -->
            <div class="card">
                <div class="card-body" style="overflow: hidden;height: 500px;overflow-y: auto;">

                    @php
                        $meetingDateTime = null;
                        $graceTimePassed = false;

                        if (isset($scheduledMeeting)) {
                            $meetingDateTime = Carbon\Carbon::createFromFormat(
                                'Y-m-d H:i:s',
                                $scheduledMeeting->meeting_date . ' ' . $scheduledMeeting->meeting_time,
                                config('app.timezone'),
                            );

                            // 30-minute grace period after scheduled time
                            $graceTimePassed = now(config('app.timezone'))->gt(
                                $meetingDateTime->copy()->addMinutes(30),
                            );
                        }

                        // Decide if rescheduling is allowed
                        $canReschedule =
                            !isset($scheduledMeeting) ||
                            in_array($scheduledMeeting->status, ['rejected', 'rescheduled']) ||
                            ($scheduledMeeting->status === 'approved' && $graceTimePassed);
                    @endphp

                    @if (isset($scheduledMeeting) && !($scheduledMeeting->status === 'approved' && $graceTimePassed))
                        <div class="mb-4">
                            <strong>Status:</strong> {!! meetingStatus($scheduledMeeting->status) !!}

                            {{-- Admin Notes --}}
                            @if (in_array($scheduledMeeting->status, ['rejected', 'rescheduled']))
                                <div class="alert alert-warning mt-2">
                                    <strong>Admin Notes:</strong> {{ $scheduledMeeting->admin_notes }}
                                </div>

                                <div class="alert alert-info mb-4">
                                    Please submit a new meeting request with a different date and time.
                                </div>
                            @endif
                        </div>
                    @endif

                    @if (isset($scheduledMeeting) && $scheduledMeeting->status === 'pending')
                        <div class="alert alert-info">
                            Your meeting request is under review.
                            You will be notified once it is approved.<br><br>

                            <strong>Requested on:</strong>
                            {{ Carbon\Carbon::parse($scheduledMeeting->created_at)->format('F j, Y \a\t g:i A') }}<br>

                            <strong>Meeting Date:</strong>
                            {{ $meetingDateTime->format('F j, Y') }}<br>

                            <strong>Meeting Time:</strong>
                            {{ $meetingDateTime->format('g:i A') }}
                        </div>
                    @endif

                    @if (isset($scheduledMeeting) && $scheduledMeeting->status === 'approved' && !$graceTimePassed)
                        <div class="alert alert-success">
                            Your meeting has been approved. Please join at the scheduled time.<br><br>

                            <strong>Meeting Date:</strong>
                            {{ $meetingDateTime->format('F j, Y') }}<br>

                            <strong>Meeting Time:</strong>
                            {{ $meetingDateTime->format('g:i A') }}
                        </div>
                    @endif

                    @if (isset($scheduledMeeting) && $scheduledMeeting->status === 'approved' && $graceTimePassed)
                        <div class="alert alert-warning mb-4">
                            You did not join the meeting at the scheduled time.
                            Since 30 minutes have passed, you may now schedule a new meeting.
                        </div>
                    @endif

                    @if (isset($scheduledMeeting) && $scheduledMeeting->status === 'verified')
                        <div class="alert alert-success mb-4">
                            <strong>✅ Meeting Completed & Verified</strong><br><br>
                            Your meeting has been successfully completed, and your identity has been verified.
                            Thank you for your cooperation. No further action is required from your side.
                        </div>
                    @endif

                    @if ($canReschedule)
                        <form method="POST" action="{{ route('user.schedule.meeting.store') }}">
                            @csrf

                            <input type="hidden" name="order_id" value="{{ $order_id }}">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Meeting Date</label>
                                    <input type="date" name="meeting_date" class="form-control"
                                        min="{{ now()->format('Y-m-d') }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Meeting Time</label>
                                    <input type="text" id="meeting_time" name="meeting_time" class="form-control"
                                        placeholder="Select time" required>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Notes (Optional)</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Any additional information..."></textarea>
                                </div>

                                <div class="col-12 text-start mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        Schedule Meeting
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif

                </div>
            </div>

        </div>
    </main>
    <!-- Main content end -->
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        // Initialize Flatpickr for time selection
        flatpickr("#meeting_time", {
            enableTime: true,
            noCalendar: true,

            // 24-hour format (DB safe for TIME column)
            dateFormat: "H:i", // stored value → 14:30
            time_24hr: true, // 24-hour picker

            minuteIncrement: 5,
            minTime: "09:00",
            maxTime: "20:00",
            defaultDate: "09:00",

            altInput: true, // user-friendly display
            altFormat: "H:i", // also 24-hour display

            allowInput: false,

            onReady: function(_, __, instance) {
                instance.calendarContainer.classList.add('shadow', 'rounded');
            }
        });
    </script>
@endsection
