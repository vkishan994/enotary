@extends('admin.layouts.common')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h4 class="py-3 mb-4">
                <span class="text-muted fw-light">NotaryServiceType /</span> List
            </h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('notary-service-types.create') }}" class="btn btn-primary">Add NotaryServiceType</a>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">NotaryServiceType List</h5>
        <div class="card-body">
            <table class="datatables-ajax table table-bordered" id="notary-service-typesTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('admin/assets/js/delete-records.js') }}"></script>

        <script>
            $(document).ready(function() {
                $('#notary-service-typesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('notary-service-types.index') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'name', name: 'name' },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    ]
                });
            });
        </script>
    @endpush
@endsection