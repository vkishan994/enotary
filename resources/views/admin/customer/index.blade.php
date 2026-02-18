@extends('admin.layouts.common')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('admin/assets/css/customer-page.css') }}" rel="stylesheet">
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <h4 class="py-3 mb-4">
                <span class="text-muted fw-light">Customers /</span> List
            </h4>
        </div>
    </div>
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
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search clients...">
                            </div>

                            <div class="customers-list">
                                @foreach ($users as $user)
                                    <a href="{{ route('customers.list', $user->id) }}">
                                        <div class="client-item {{ $selectedUser->id == $user->id ? 'active' : '' }}">
                                            <div class="client-name">{{ $user->first_name }} {{ $user->last_name }}</div>
                                            <div class="status-badge">
                                                <span class="status-text">{{ $user->orders_count }} Orders</span>
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
                                                            <a href="{{ route('customers.list', [$selectedUser->id, $order->id]) }}"
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

                                        {{-- <div class="detail-item">
                                            <div class="detail-label">Provider:</div>
                                            <div class="detail-value">Onfido</div>
                                        </div> --}}
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
                                            <div class="detail-value">Power of Attorney</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-value">poa_aisha.pdf</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Reviewed by:</div>
                                            <div class="detail-value">M. Edwards</div>
                                        </div>
                                        <span class="approval-badge">Approved</span>
                                    </div>

                                    <div class="detail-card">
                                        <h6>Meeting Details</h6>
                                        <div class="detail-item">
                                            <div class="detail-label">Status:</div>
                                            <div class="detail-value">Not Scheduled</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-value">Awaiting Document Approval.</div>
                                        </div>
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
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    <i class="bx bx-info-circle me-2"></i>
                    <strong>No clients found</strong>
                    <p class="mb-0 mt-2">There are currently no registered clients in the system.</p>
                </div>
            </div>
        @endif
    </div>
    @include('partials.customer_page_modal')
@endsection
