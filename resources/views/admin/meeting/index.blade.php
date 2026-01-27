@extends('admin.layouts.common')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h4 class="py-3 mb-4">
                <span class="text-muted fw-light">Schedule Meetings /</span> List
            </h4>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">All Schedule Meetings</h5>
        <div class="card-body">
            <table class="datatables-ajax table table-bordered" id="ordersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Meeting Date</th>
                        <th>Meeting Time</th>
                        <th>Status</th>
                        <th>User Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#ordersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.schedule.meetings.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'user_name',
                            name: 'user_name'
                        },
                        {
                            data: 'user_email',
                            name: 'user_email'
                        },
                        {
                            data: 'meeting_date',
                            name: 'meeting_date'
                        },
                        {
                            data: 'meeting_time',
                            name: 'meeting_time'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'notes',
                            name: 'notes'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            });
        </script>
    @endpush
@endsection
