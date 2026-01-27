@extends('admin.layouts.common')

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Schedule Meetings /</span> Meeting Details
    </h4>

    <x-alert type="success" :message="session('success')" />
    <x-alert type="danger" :message="session('error')" />

    <div class="row">
        <div class="col-md-12">

            <div class="card shadow-sm border-0 mb-4">

                <!-- Header -->
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">Meeting Details</h5>
                    {!! meetingStatus($meeting->status) !!}
                </div>

                <div class="card-body">

                    <!-- Meeting Info -->
                    <div class="row text-center mb-4">
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <small class="text-muted">Date</small>
                                <div class="fw-semibold mt-1">
                                    {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <small class="text-muted">Time</small>
                                <div class="fw-semibold mt-1">
                                    {{ $meeting->meeting_time }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
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

                        <div class="row">
                            <div class="col-md-6">

                                <!-- Status -->
                                <div class="mb-3">
                                    <label class="form-label">Change Status</label>
                                    <select name="status" id="statusSelect" class="form-select" required>
                                        <option value="">Select Status</option>
                                        <option value="approved" {{ $meeting->status == 'approved' ? 'selected' : '' }}>
                                            Approve
                                        </option>
                                        <option value="rejected" {{ $meeting->status == 'rejected' ? 'selected' : '' }}>
                                            Reject
                                        </option>
                                        <option value="rescheduled"
                                            {{ $meeting->status == 'rescheduled' ? 'selected' : '' }}>
                                            Reschedule
                                        </option>
                                    </select>
                                </div>

                                <!-- Notes (below status, same width) -->
                                <div class="mb-3 d-none" id="rejectNotes">
                                    <label class="form-label">Rejection Notes</label>
                                    <textarea name="admin_notes" class="form-control" rows="3" placeholder="Please provide a reason for rejection..."></textarea>
                                </div>

                                <div class="mb-3 d-none" id="rescheduleNotes">
                                    <label class="form-label">Reschedule Notes</label>
                                    <textarea name="admin_notes" class="form-control" rows="3" placeholder="Please provide a reason for rescheduling..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Update Status
                                </button>

                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const statusSelect = document.getElementById('statusSelect');
                const rejectNotes = document.getElementById('rejectNotes');
                const rescheduleNotes = document.getElementById('rescheduleNotes');

                function toggleNotes() {
                    if (statusSelect.value === 'rejected') {
                        rejectNotes.classList.remove('d-none');
                    } else {
                        rejectNotes.classList.add('d-none');
                    }

                    if (statusSelect.value === 'rescheduled') {
                        rescheduleNotes.classList.remove('d-none');
                    } else {
                        rescheduleNotes.classList.add('d-none');
                    }
                }

                statusSelect.addEventListener('change', toggleNotes);
                toggleNotes(); // on page load
            });
        </script>
    @endpush
@endsection
