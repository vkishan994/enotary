@extends('admin.layouts.common')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{asset('admin/assets/css/customer-page.css')}}" rel="stylesheet">
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

                            @foreach ($users as $user)
                                <a href="{{ route('customers.list', $user->id) }}">
                                    <div class="client-item {{ $selectedUser->id == $user->id ? 'active' : '' }}">
                                        <div class="client-name">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <div class="status-badge">
                                            <i class="fas fa-check-circle status-icon text-success"></i>
                                            <span class="status-text">Doc Approved</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach

                        </div>
                    </div>

                    <div class="col-lg-9">
                        <!-- Main Content -->
                        <div class="main-content">
                            <!-- Client Header -->
                            <div class="client-header">
                                <div class="client-avatar">AK</div>
                                <div class="client-info">
                                    <h2>{{ $selectedUser->first_name }} {{ $selectedUser->last_name }}</h2>
                                    <div class="client-id">Client ID: {{ $selectedUser->id }}</div>
                                    <div class="contact-info">
                                        <span><i class="fas fa-envelope"></i> {{ $selectedUser->email }}</span>
                                        {{-- <span><i class="fas fa-phone"></i> +44 7700 900123</span> --}}
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Steps -->
                            <div class="progress-section">
                                <h5>Progress Steps</h5>
                                <div class="progress-steps">
                                    <div class="step-item">
                                        <div class="step-header">
                                            <div class="step-icon complete">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="step-title">Upload Document</div>
                                        </div>
                                        <span class="step-badge badge-complete">Complete</span>
                                    </div>
                                    <div class="step-item">
                                        <div class="step-header">
                                            <div class="step-icon locked">
                                                <i class="fas fa-lock"></i>
                                            </div>
                                            <div class="step-title">Verify Identity</div>
                                        </div>
                                        <span class="step-badge badge-locked">Locked</span>
                                    </div>
                                    <div class="step-item">
                                        <div class="step-header">
                                            <div class="step-icon pending">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                            <div class="step-title">Schedule Meeting</div>
                                        </div>
                                    </div>
                                    <div class="step-item">
                                        <div class="step-header">
                                            <div class="step-icon pending">
                                                <i class="fas fa-download"></i>
                                            </div>
                                            <div class="step-title">Download Docs</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Details Grid -->
                            <div class="details-grid">
                                <div class="detail-card">
                                    <h6>Identity Verification</h6>
                                    <div class="detail-item">
                                        <div class="detail-label">Status:</div>
                                        <div class="detail-value">Not Started</div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Provider:</div>
                                        <div class="detail-value">Onfido</div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-value">KYC-8BA1</div>
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
@endsection
