@extends('front.layouts.common')

@section('content')
    @include('front.layouts.dashboard.sidebar')

    <!-- Main content start -->
    <main class="main-content">

        <div class="document-upload document-pending">
            <div class="section-title mb-3">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h4>Schedule eNotary Meeting</h4>
                    </div>
                </div>
            </div>

            <x-alert type="success" :message="session('success')" />
            <x-alert type="danger" :message="session('error')" />

            <!-- Schedule Form -->
            <div class="card">
                <div class="card-body">

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
                                <input type="time" name="meeting_time" class="form-control" required>
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

                </div>
            </div>

        </div>
    </main>
    <!-- Main content end -->
@endsection
