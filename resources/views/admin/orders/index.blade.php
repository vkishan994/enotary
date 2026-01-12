@extends('admin.layouts.common')
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
                    <th>#</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Document</th>
                    <th>Service Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
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
                    data: 'document',
                    name: 'document'
                },
                {
                    data: 'service_type',
                    name: 'service_type'
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
                    data: 'date',
                    name: 'date'
                },
            ]
        });
    });
</script>
@endpush
@endsection