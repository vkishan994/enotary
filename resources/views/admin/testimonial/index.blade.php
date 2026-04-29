@extends('admin.layouts.common')
@section('title', 'Testimonials - List')
@section('content')
    <div class="row align-items-center mb-3">
        <div class="col-sm-6">
            <h4 class="py-sm-3 mb-2 mb-sm-4">
                <span class="text-muted fw-light">Testimonials /</span> List
            </h4>
        </div>
        <div class="col-sm-6 text-sm-end text-start">
            <a href="{{ route('testimonials.create') }}" class="btn btn-primary mb-2 mb-sm-0">Add Testimonial</a>
        </div>
    </div>


    <!-- Ajax Sourced Server-side -->
    <div class="card">
        <h5 class="card-header">Testimonials List</h5>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="datatables-ajax table table-bordered" id="testimonialsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('admin/assets/js/delete-records.js') }}"></script>

        <script>
            $(document).ready(function() {

                var table = $('#testimonialsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('testimonials.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'rating',
                            name: 'rating'
                        },
                        {
                            data: 'status',
                            name: 'status'
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
