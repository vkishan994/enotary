@extends('admin.layouts.common')
@section('title', 'Customers - List')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('admin/assets/css/customer-page.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <h4 class="py-3 mb-4">
                <span class="text-muted fw-light">Customers /</span> List
            </h4>
        </div>
    </div>

    <form method="GET" class="row mb-3">

        <div class="col-md-3">
            <select name="payment_status" class="form-control">
                <option value="">Payment</option>
                <option value="completed" {{ request('payment_status') == 'completed' ? 'selected' : '' }}>Completed
                </option>
                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </div>

        <!-- Date Range Picker -->
        <div class="col-md-3">
            <input type="text" id="dateRange" class="form-control" placeholder="Select date range">

            <input type="hidden" name="from_date" id="from_date" value="{{ request('from_date') }}">
            <input type="hidden" name="to_date" id="to_date" value="{{ request('to_date') }}">
        </div>

        <div class="col-md-3">
            <select name="pending_step" class="form-control">
                <option value="">Pending Steps</option>
                <option value="veriff" {{ request('pending_step') == 'veriff' ? 'selected' : '' }}>Verify Identity</option>
                <option value="documents" {{ request('pending_step') == 'documents' ? 'selected' : '' }}>Upload Document
                </option>
                <option value="meeting" {{ request('pending_step') == 'meeting' ? 'selected' : '' }}>Schedule Meeting
                </option>
                {{-- <option value="enotary" {{ request('pending_step') == 'enotary' ? 'selected' : '' }}>Download / Notarisation
                </option> --}}
            </select>
        </div>

        <div class="col-md-1">
            <button class="btn btn-primary w-100">Filter</button>
        </div>

        <div class="col-md-2">
            <a href="{{ route('customers.list') }}" class="btn btn-secondary w-100">Reset Filters</a>
        </div>

    </form>

    <div class="main-container">
        <!-- Top Navigation -->
        {{-- <div class="top-nav d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-4">
                <h1 class="admin-title">Admin</h1>
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Clients</a>
                    </li>
                </ul>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary">Message Client</button>
                <button class="btn btn-outline-secondary dropdown-toggle">More</button>
                <button class="btn btn-outline-secondary"><i class="fas fa-user"></i></button>
            </div>
        </div> --}}

        @if ($users->count() > 0)
            <!-- Main Content Area -->
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-lg-3 sidebar">
                        <!-- Sidebar -->
                        <div class="p-2">
                            <div class="search-box">
                                {{-- <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search clients..."> --}}
                                <form method="GET" action="{{ route('customers.list', request()->route('id')) }}">
                                    <i class="fas fa-search"></i>
                                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                        placeholder="Search clients..." class="form-control">
                                </form>
                            </div>

                            <div class="customers-list" id="customersList">
                                @foreach ($users as $user)
                                    {{-- <a href="{{ route('customers.list', $user->id) }}"> --}}
                                    <a
                                        href="{{ route('customers.list', $user->id) . '?' . http_build_query(request()->except('id')) }}">
                                        <div class="client-item {{ $selectedUser->id == $user->id ? 'active' : '' }}">
                                            <div class="client-name">{{ $user->first_name }} {{ $user->last_name }}</div>
                                            <div class="status-badge">
                                                {{-- <span class="status-text">{{ $user->orders_count }} Orders</span> --}}
                                                <span class="status-text">{{ $user->filtered_orders_count }} Orders</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-9">
                        <!-- Main Content -->
                        <div class="main-content">
                            <!-- Client Header -->
                            <div class="client-header">
                                <div class="client-avatar">
                                    {{ strtoupper(substr($selectedUser->first_name, 0, 1) . substr($selectedUser->last_name, 0, 1)) }}
                                </div>
                                <div class="client-info">
                                    <h2>{{ $selectedUser->first_name }} {{ $selectedUser->last_name }}</h2>
                                    <div class="client-id">Client ID: {{ $selectedUser->id }}</div>
                                    <div class="contact-info">
                                        <span><i class="fas fa-envelope"></i> {{ $selectedUser->email }}</span>
                                    </div>
                                </div>
                            </div>

                            @if ($selectedUser && $selectedUser->orders->count() > 0)
                                <h5>Orders List</h5>
                                <div class="order-details-table">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Payment Status</th>
                                                    <th>Date</th>
                                                    <th>Steps Completed</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse ($orders as $order)
                                                    <tr>
                                                        <td>#{{ $order->id }}</td>
                                                        <td>
                                                            {!! paymentStatus($order->payment_status) !!}
                                                        </td>
                                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                                        <td>
                                                            {{ orderStepsCompletedCount($order) ?? '0' }} / 4
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('customers.list', [$selectedUser->id, $order->id]) . '?' . http_build_query(request()->query()) }}"
                                                                class="btn btn-sm btn-primary">
                                                                View Details
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">
                                                            No orders found
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <div class="mt-3">
                                            {{ $orders->links() }}
                                        </div>
                                    </div>
                                </div>

                                @if ($selectedOrder)
                                    <div class="card mt-4 shadow-sm border-0 mb-4 current-order">
                                        <div class="card-body text-center">
                                            <p class="text-muted mb-2">Current Order</p>
                                            <h3 class="fw-bold text-primary mb-0">
                                                #{{ $selectedOrder->id }}
                                            </h3>
                                        </div>
                                    </div>
                                @endif

                                <!-- Progress Steps -->
                                @php
                                    $steps = orderStepStatus($selectedOrder);
                                @endphp

                                <div class="progress-section">
                                    <h5>Progress Steps</h5>
                                    <div class="progress-steps">

                                        {{-- 1. Verify Identity --}}
                                        <div class="step-item">
                                            <div class="step-header">
                                                <div class="step-icon {{ $steps['veriff'] }}">
                                                    <i
                                                        class="fas {{ $steps['veriff'] == 'complete' ? 'fa-check' : ($steps['veriff'] == 'pending' ? 'fa-spinner' : 'fa-lock') }}">
                                                    </i>
                                                </div>
                                                <div class="step-title">Verify Identity</div>
                                            </div>
                                            <span class="step-badge badge-{{ $steps['veriff'] }}">
                                                {{ ucfirst($steps['veriff']) }}
                                            </span>
                                        </div>

                                        {{-- 2. Upload Document --}}
                                        <div class="step-item">
                                            <div class="step-header">
                                                <div class="step-icon {{ $steps['documents'] }}">
                                                    <i
                                                        class="fas {{ $steps['documents'] == 'complete' ? 'fa-check' : ($steps['documents'] == 'pending' ? 'fa-spinner' : 'fa-lock') }}">
                                                    </i>
                                                </div>
                                                <div class="step-title">Upload Document</div>
                                            </div>
                                            <span class="step-badge badge-{{ $steps['documents'] }}">
                                                {{ ucfirst($steps['documents']) }}
                                            </span>
                                        </div>

                                        {{-- 3. Schedule Meeting --}}
                                        <div class="step-item">
                                            <div class="step-header">
                                                <div class="step-icon {{ $steps['meeting'] }}">
                                                    <i
                                                        class="fas {{ $steps['meeting'] == 'complete' ? 'fa-check' : ($steps['meeting'] == 'pending' ? 'fa-spinner' : 'fa-lock') }}">
                                                    </i>
                                                </div>
                                                <div class="step-title">Schedule Meeting</div>
                                            </div>
                                            <span class="step-badge badge-{{ $steps['meeting'] }}">
                                                {{ ucfirst($steps['meeting']) }}
                                            </span>
                                        </div>

                                        {{-- 4. Download Docs --}}
                                        <div class="step-item">
                                            <div class="step-header">
                                                <div class="step-icon {{ $steps['enotary'] }}">
                                                    <i
                                                        class="fas {{ $steps['enotary'] == 'complete' ? 'fa-check' : ($steps['enotary'] == 'pending' ? 'fa-spinner' : 'fa-lock') }}">
                                                    </i>
                                                </div>
                                                <div class="step-title">Download Docs</div>
                                            </div>
                                            <span class="step-badge badge-{{ $steps['enotary'] }}">
                                                {{ ucfirst($steps['enotary']) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Details Grid -->
                                <div class="details-grid">
                                    <div class="detail-card">
                                        <h6>Identity Verification</h6>
                                        <div class="detail-item">
                                            <div class="detail-label">Status:</div>
                                            <div class="detail-value">{!! veriffStatus(isset($selectedOrder->veriffData) ? $selectedOrder->veriffData->status : null) !!}</div>
                                        </div>

                                        @if (isset($selectedOrder->veriffData) && $selectedOrder->veriffData->status == 'approved')
                                            <div class="detail-item">
                                                <div class="detail-value">{{ $selectedOrder->veriffData->session_id }}
                                                </div>
                                            </div>
                                        @endif

                                        <div class="detail-item">
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#veriffModal">
                                                View
                                            </button>
                                        </div>
                                    </div>

                                    <div class="detail-card">
                                        <h6>Uploaded Document</h6>
                                        <div class="detail-item">
                                            <div class="detail-value">
                                                {{ isset($selectedOrder->document->name) ? $selectedOrder->document->name : 'N/A' }}
                                            </div>
                                        </div>
                                        @if ($selectedOrder->hasUserUploadedAllDocuments($selectedOrder->user_id))
                                            <div class="detail-item">
                                                <div class="detail-label">Reviewed by:</div>
                                                <div class="detail-value">
                                                    @php
                                                        $reviewedDoc =
                                                            $selectedOrder->verifyDocuments->firstWhere(
                                                                'status',
                                                                'approved',
                                                            ) ??
                                                            ($selectedOrder->verifyDocuments->firstWhere(
                                                                'status',
                                                                'rejected',
                                                            ) ??
                                                                $selectedOrder->verifyDocuments->firstWhere(
                                                                    'status',
                                                                    'verified',
                                                                ));
                                                    @endphp

                                                    {{ optional($reviewedDoc?->admin)->name ?? 'Not Reviewed' }}
                                                </div>
                                            </div>
                                        @endif
                                        <div class="detail-item">
                                            <div class="detail-label">Status:</div>
                                            {!! documentUploadStatus($selectedOrder->upload_document_status) !!}
                                        </div>

                                        @if ($selectedOrder->hasUserUploadedAllDocuments($selectedOrder->user_id))
                                            <div class="detail-item">
                                                <a href="{{ route('verifyDocument', ['order_id' => $selectedOrder->id]) }}"
                                                    class="btn btn-sm btn-primary">
                                                    View
                                                </a>
                                            </div>
                                        @endif


                                        {{-- <div class="detail-item">
                                            <a href="{{ route('verifyDocument', ['order_id' => $selectedOrder->id]) }}"
                                                class="btn btn-sm btn-primary">
                                                View
                                            </a>
                                        </div> --}}
                                    </div>

                                    <div class="detail-card">
                                        <h6>Meeting Details</h6>
                                        <div class="detail-item">
                                            <div class="detail-label">Status:</div>
                                            <div class="detail-value">
                                                @if (isset($selectedOrder->scheduleMeeting))
                                                    {!! meetingStatus($selectedOrder->scheduleMeeting->status) !!}
                                                @else
                                                    {!! meetingStatus('') !!}
                                                @endif
                                            </div>
                                        </div>
                                        @if (!$selectedOrder->all_docs_verified)
                                            <div class="detail-item">
                                                <div class="detail-value">Awaiting Document Approval.</div>
                                            </div>
                                        @endif
                                        @if (isset($selectedOrder->scheduleMeeting))
                                            <div class="detail-item">
                                                <a href="{{ route('scheduleMeeting', ['id' => $selectedOrder->scheduleMeeting->id]) }}"
                                                    class="btn btn-sm btn-primary">
                                                    View
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="detail-card">
                                        <h6>Notarised Documents</h6>
                                        <div class="detail-item">
                                            <div class="detail-label">Status:</div>
                                            <div class="detail-value">Not Ready</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-value">Documents pending meeting.</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Show this if no orders -->
                                <div class="card mb-4">
                                    <div class="card-body text-center">
                                        <h6 class="mb-2">No Orders Found</h6>
                                        <p class="text-muted mb-0">
                                            This client has not placed any orders yet.
                                        </p>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 60vh;">
                <div class="card border-0 shadow-lg text-center p-5 position-relative overflow-hidden"
                    style="max-width: 520px; border-radius: 20px;">

                    <!-- Soft Background Accent -->
                    <div class="position-absolute top-0 start-50 translate-middle-x"
                        style="width: 200px; height: 200px; background: linear-gradient(135deg, #eef2ff, #f8fafc);
                    border-radius: 50%; z-index: 0; filter: blur(40px); opacity: 0.7;">
                    </div>

                    <div class="position-relative" style="z-index: 1;">

                        <!-- Icon Section -->
                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm"
                                style="width: 90px; height: 90px; background: linear-gradient(135deg, #6366f1, #4f46e5);">
                                <i class="bx bx-user-x text-white" style="font-size: 40px;"></i>
                            </div>
                        </div>

                        <!-- Heading -->
                        <h4 class="fw-bold mb-2">No Customers Found</h4>

                        <!-- Description -->
                        <p class="text-muted mb-4" style="font-size: 15px;">
                            There are currently no registered customers in the system.
                            Once users sign up, they will appear here.
                        </p>

                    </div>
                </div>
            </div>
        @endif
    </div>
    @include('partials.customer_page_modal')
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                const fromDate = document.getElementById('from_date').value;
                const toDate = document.getElementById('to_date').value;

                const picker = new Litepicker({
                    element: document.getElementById('dateRange'),
                    singleMode: false,
                    format: 'YYYY-MM-DD',

                    // Set default selected dates
                    startDate: fromDate ? fromDate : null,
                    endDate: toDate ? toDate : null,

                    setup: (picker) => {
                        picker.on('selected', (start, end) => {
                            document.getElementById('from_date').value = start.format('YYYY-MM-DD');
                            document.getElementById('to_date').value = end.format('YYYY-MM-DD');
                        });
                    }
                });

                // Optional: show selected range in input manually
                if (fromDate && toDate) {
                    document.getElementById('dateRange').value = fromDate + '  -  ' + toDate;
                }
            });

            const customerBaseUrl = "{{ url('admin/customers') }}";
            document.getElementById('searchInput').addEventListener('keyup', function() {

                let searchValue = this.value;

                fetch("{{ route('customers.search') }}?search=" + searchValue)
                    .then(response => response.json())
                    .then(data => {

                        let customersList = document.getElementById('customersList');
                        customersList.innerHTML = '';

                        if (data.length === 0) {
                            customersList.innerHTML = '<p class="p-2">No users found</p>';
                            return;
                        }

                        data.forEach(user => {

                            customersList.innerHTML += `
                    <a href="${customerBaseUrl}/${user.id}">
                        <div class="client-item">
                            <div class="client-name">
                                ${user.first_name} ${user.last_name}
                            </div>
                            <div class="status-badge">
                                <span class="status-text">
                                    ${user.orders_count} Orders
                                </span>
                            </div>
                        </div>
                    </a>
                `;
                        });

                    });

            });
        </script>
    @endpush
@endsection
