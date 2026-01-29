@extends('admin.layouts.common')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
@endsection
@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Schedule Meetings /</span> Meeting Details
    </h4>

    <x-alert type="success" :message="session('success')" />
    <x-alert type="danger" :message="session('error')" />

    <div class="container">
        <h4 class="mb-4">Calendar</h4>

        <div class="row g-4">
            <!-- Calendar column -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3">
                        <div id="calendar" style="max-width: 100%;"></div>
                    </div>
                </div>


                {{-- <iframe
                    src="https://calendar.google.com/calendar/embed?src={{ urlencode(getValuesByKey('google_calendar_id') ?? 'primary@gmail.com') }}&ctz=Asia/Kolkata"
                    style="border: 0" width="100%" height="600" frameborder="0" scrolling="no">
                </iframe> --}}
            </div>

            <!-- Meeting Details and Update Form column -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0">

                    <!-- Header -->
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">Meeting Details</h5>
                        {!! meetingStatus($meeting->status) !!}
                    </div>

                    <div class="card-body">

                        <!-- Meeting Info -->
                        <div class="row text-center mb-4">
                            <div class="col-4">
                                <div class="p-3 border rounded bg-light">
                                    <small class="text-muted">Date</small>
                                    <div class="fw-semibold mt-1">
                                        {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="p-3 border rounded bg-light">
                                    <small class="text-muted">Time</small>
                                    <div class="fw-semibold mt-1">
                                        {{ $meeting->meeting_time }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="p-3 border rounded bg-light">
                                    <small class="text-muted">User</small>
                                    <div class="fw-semibold mt-1">
                                        {{ $meeting->user->first_name . ' ' . $meeting->user->last_name ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Status Update -->
                        <h6 class="mb-3 fw-semibold">Update Meeting Status</h6>

                        <form method="POST" action="{{ route('admin.schedule.meetings.update', $meeting->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="statusSelect" class="form-label">Change Status</label>
                                <select name="status" id="statusSelect" class="form-select" required>
                                    <option value="">Select Status</option>

                                    <option value="approved" {{ $meeting->status === 'approved' ? 'selected' : '' }}>
                                        Approve
                                    </option>

                                    @if ($meeting->status !== 'approved')
                                        <option value="rejected" {{ $meeting->status === 'rejected' ? 'selected' : '' }}>
                                            Reject
                                        </option>
                                    @endif

                                    <option value="rescheduled" {{ $meeting->status === 'rescheduled' ? 'selected' : '' }}>
                                        Reschedule
                                    </option>
                                </select>
                            </div>

                            @php
                                $showNotes =
                                    in_array($meeting->status, ['rejected', 'rescheduled']) &&
                                    !empty($meeting->admin_notes);
                            @endphp

                            <div class="mb-3 {{ $showNotes ? '' : 'd-none' }}" id="adminNotesWrapper">
                                <label class="form-label" id="adminNotesLabel">
                                    {{ $meeting->status === 'rejected' ? 'Rejection Notes' : 'Reschedule Notes' }}
                                </label>

                                <textarea name="admin_notes" id="adminNotesTextarea" class="form-control" rows="3"
                                    placeholder="{{ $meeting->status === 'rejected'
                                        ? 'Please provide a reason for rejection...'
                                        : 'Please provide a reason for rescheduling...' }}">{{ $meeting->admin_notes }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Update Status
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for event details -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="eventDateTime"></p>

                    <p>
                        <a href="#" target="_blank" id="googleMeetLink" class="btn btn-sm btn-outline-primary d-none">
                            Join with Google Meet
                        </a>
                    </p>

                    <p id="attendeesInfo"></p>
                    <div id="attendeesList" class="mb-2"></div>

                    <p id="eventDescription"></p>
                    <p><small id="organizerInfo" class="text-muted"></small></p>
                </div>
                <div class="modal-footer">
                    <a href="#" id="openInGoogleCal" target="_blank" class="btn btn-primary">View in Google
                        Calendar</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');
                const modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: 650,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: "{{ route('admin.calendar.events') }}", // your API route returning events with htmlLink
                    eventClick: function(info) {
                        const event = info.event;
                        const props = event.extendedProps;

                        // --- Modal title ---
                        document.getElementById('eventTitle').textContent = event.title;

                        // --- Date & time ---
                        const start = new Date(event.start);
                        const end = new Date(event.end);
                        const options = {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        };
                        const dateStr = start.toLocaleDateString(undefined, options);
                        const timeStr = start.toLocaleTimeString(undefined, {
                                hour: '2-digit',
                                minute: '2-digit'
                            }) +
                            ' – ' +
                            end.toLocaleTimeString(undefined, {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        document.getElementById('eventDateTime').textContent = `${dateStr} · ${timeStr}`;

                        // --- Google Meet link ---
                        const meetLinkEl = document.getElementById('googleMeetLink');
                        if (props.conference) {
                            meetLinkEl.href = props.conference;
                            meetLinkEl.classList.remove('d-none');
                        } else {
                            meetLinkEl.classList.add('d-none');
                        }

                        // --- Attendees info ---
                        const attendeesInfoEl = document.getElementById('attendeesInfo');
                        const attendeesCount = props.attendeesCount || 0;
                        const attendeesWaiting = props.attendeesWaiting || 0;
                        if (attendeesCount) {
                            attendeesInfoEl.textContent =
                                `${attendeesCount} guest${attendeesCount > 1 ? 's' : ''}, ${attendeesWaiting} awaiting`;
                        } else {
                            attendeesInfoEl.textContent = '';
                        }

                        // --- Attendees list ---
                        const attendeesListEl = document.getElementById('attendeesList');
                        attendeesListEl.innerHTML = '';
                        if (props.attendees && props.attendees.length) {
                            props.attendees.forEach(function(att) {
                                let icon = att.self ? '🧑' : '👤';
                                attendeesListEl.innerHTML += `<div>${icon} ${att.email}</div>`;
                            });
                        }

                        // --- Description ---
                        document.getElementById('eventDescription').textContent = props.description || '';

                        // --- Organizer ---
                        const organizerInfoEl = document.getElementById('organizerInfo');
                        if (props.organizerDisplayName || props.organizer) {
                            organizerInfoEl.textContent =
                                `Created by: ${props.organizerDisplayName || props.organizer}`;
                        } else {
                            organizerInfoEl.textContent = '';
                        }

                        // --- Open in Google Calendar button ---
                        const openInGoogleCalBtn = document.getElementById('openInGoogleCal');
                        if (props.htmlLink) {
                            openInGoogleCalBtn.href = props.htmlLink; // Link opens in new tab
                            openInGoogleCalBtn.classList.remove('d-none');
                        } else {
                            openInGoogleCalBtn.classList.add('d-none');
                        }

                        // Show modal
                        modal.show();
                    }
                });

                calendar.render();
            });


            document.addEventListener('DOMContentLoaded', function() {
                const statusSelect = document.getElementById('statusSelect');
                const wrapper = document.getElementById('adminNotesWrapper');
                const label = document.getElementById('adminNotesLabel');
                const textarea = document.getElementById('adminNotesTextarea');

                // Store initial notes (from DB)
                const existingNotes = textarea.value.trim();

                function toggleNotes() {
                    if (statusSelect.value === 'rejected') {
                        wrapper.classList.remove('d-none');
                        label.innerText = 'Rejection Notes';
                        textarea.placeholder = 'Please provide a reason for rejection...';
                        textarea.required = true;

                        // Restore old notes if textarea is empty
                        if (!textarea.value && existingNotes) {
                            textarea.value = existingNotes;
                        }

                    } else if (statusSelect.value === 'rescheduled') {
                        wrapper.classList.remove('d-none');
                        label.innerText = 'Reschedule Notes';
                        textarea.placeholder = 'Please provide a reason for rescheduling...';
                        textarea.required = true;

                        if (!textarea.value && existingNotes) {
                            textarea.value = existingNotes;
                        }

                    } else {
                        wrapper.classList.add('d-none');
                        textarea.required = false;
                        // DO NOT clear textarea value
                    }
                }

                // Init on page load
                toggleNotes();

                statusSelect.addEventListener('change', toggleNotes);
            });
        </script>
    @endpush
@endsection
