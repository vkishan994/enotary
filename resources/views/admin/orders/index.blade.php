@extends('admin.layouts.common')
@section('title', 'Orders - List')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h4 class="py-3 mb-4">
                <span class="text-muted fw-light">Orders /</span> List
            </h4>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Payment Status</label>
                    <select id="filter_payment_status" class="form-select filter-input">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Document Status</label>
                    <select id="filter_document_status" class="form-select filter-input">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="submitted">Submitted</option>
                        <option value="verified">Verified</option>
                        <option value="reupload">Re-upload Required</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Document</label>
                    <select id="filter_document_id" class="form-select filter-input">
                        <option value="">All</option>
                        @foreach ($documents as $doc)
                            <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary w-100" id="reset_filters">Reset Filters</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">All Orders</h5>
        <div class="card-body">
            <table class="datatables-ajax table table-bordered" id="ordersTable">
                <thead>
                    <tr>
                        <th>Order Id</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Document</th>
                        <th>Amount</th>
                        <th>Payment Status</th>
                        <th>Document Status</th>
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
                var table = $('#ordersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.orders.index') }}",
                        data: function(d) {
                            d.payment_status = $('#filter_payment_status').val();
                            d.document_status = $('#filter_document_status').val();
                            d.document_id = $('#filter_document_id').val();
                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id',
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
                            data: 'document',
                            name: 'document'
                        },
                        {
                            data: 'amount',
                            name: 'amount'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'upload_document_status',
                            name: 'upload_document_status'
                        },
                        {
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });

                $('.filter-input').change(function() {
                    table.draw();
                });

                $('#reset_filters').click(function() {
                    $('.filter-input').val('');
                    table.draw();
                });
            });
        </script>
    @endpush
@endsection
