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
                </div>
            </div>

            <x-alert type="success" :message="session('success')" />
            <x-alert type="danger" :message="session('error')" />

            <!-- Schedule Form -->
            <div class="card">
                <div class="card-body" style="overflow: hidden;height: 500px;overflow-y: auto;">

                    @if (isset($scheduledMeeting))
                        <div class="mb-4">
                            <strong>Status:</strong> {!! meetingStatus($scheduledMeeting->status) !!}

                            @if (in_array($scheduledMeeting->status, ['rejected', 'rescheduled']) && $scheduledMeeting->notes)
                                <div class="alert alert-warning mt-2">
                                    <strong>Admin Notes:</strong> {{ $scheduledMeeting->notes }}
                                </div>
                            @endif

                            @if ($scheduledMeeting->status === 'rescheduled' || $scheduledMeeting->status === 'rejected')
                                <div class="alert alert-info mt-2">
                                    Please submit a new meeting request with a different date and time.
                                </div>
                            @endif
                        </div>
                    @endif

                    @if (isset($scheduledMeeting) && $scheduledMeeting->status == 'pending')
                        <div class="alert alert-info">
                            Your meeting request is under review.
                            You will be notified as soon as it is reviewed and approved.<br><br>

                            <strong>Requested on:</strong>
                            {{ \Carbon\Carbon::parse($scheduledMeeting->created_at)->format('F j, Y \a\t g:i A') }}
                        </div>
                    @elseif(isset($scheduledMeeting) && $scheduledMeeting->status == 'approved')
                        <div class="alert alert-success">
                            Your meeting has been approved! Please be ready at the scheduled time.<br><br>

                            <strong>Meeting Date:</strong>
                            {{ \Carbon\Carbon::parse($scheduledMeeting->meeting_date)->format('F j, Y') }}<br>

                            <strong>Meeting Time:</strong>
                            {{ \Carbon\Carbon::parse($scheduledMeeting->meeting_time)->format('g:i A') }}
                        </div>
                    @else
                        <form method="POST" action="{{ route('user.schedule.meeting.store') }}">
                            @csrf

                            <div class="row g-3">

                                <!-- Date -->
                                <div class="col-md-6">
                                    <label class="form-label">Meeting Date</label>
                                    <input type="date" name="meeting_date" class="form-control"
                                        min="{{ now()->format('Y-m-d') }}" required>
                                </div>

                                <input type="hidden" name="order_id" value="{{ $order_id }}">

                                <!-- Time -->
                                <div class="col-md-6">
                                    <label class="form-label">Meeting Time</label>
                                    <input type="text" id="meeting_time" name="meeting_time" class="form-control"
                                        placeholder="Select time" required>
                                </div>

                                <!-- Notes -->
                                <div class="col-md-12">
                                    <label class="form-label">Notes (Optional)</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Any additional information..."></textarea>
                                </div>

                                <!-- Submit -->
                                <div class="col-12 text-end mt-3">
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
            dateFormat: "h:i K", // 12-hour format with AM/PM
            time_24hr: false, // show AM/PM
            minuteIncrement: 5, // 15-minute steps
            minTime: "09:00",
            maxTime: "20:00",
            defaultDate: "09:00",
            altInput: true, // nicer input field
            altFormat: "h:i K", // display in 12-hour format
            wrap: false,
            allowInput: false, // prevents typing, only pick from dropdown
            onReady: function(selectedDates, dateStr, instance) {
                // add a Bootstrap-like shadow
                instance.calendarContainer.classList.add('shadow', 'rounded');
            }
        });
    </script>
@endsection
