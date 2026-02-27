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
                $('#ordersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.orders.index') }}",
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
            });
        </script>
    @endpush
@endsection
