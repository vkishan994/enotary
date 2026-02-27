@extends('admin.layouts.common')
@section('title', 'Veriff Data - List')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h4 class="py-3 mb-4">
                <span class="text-muted fw-light">EKYC Verification /</span> List
            </h4>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">EKYC Verification</h5>
        <div class="card-body">
            <table class="datatables-ajax table table-bordered" id="ekycTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order ID</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Date</th>
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
                $('#ekycTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.veriffdata.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'order_id',
                            name: 'order_id'
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
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
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
