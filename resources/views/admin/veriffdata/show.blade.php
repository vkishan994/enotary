@extends('admin.layouts.common')
@section('css')
    <style>
        .info-box {
            background: #f9fafb;
            border-radius: 8px;
            padding: 16px 18px;
            height: 100%;
        }

        .info-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .info-value {
            font-size: 14px;
            color: #111827;
            font-weight: 500;
            word-break: break-word;
        }
    </style>
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="page-title mb-0">
            <span class="text-muted">EKYC Verification /</span> Details
        </h4>

        <a href="{{ route('admin.veriffdata.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bx bx-left-arrow-alt"></i> Back
        </a>
    </div>

    <!-- ===== Order Summary ===== -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Verification Summary</h5>
        </div>

        <div class="card-body">
            <div class="row g-4 mt-2">

                <!-- Customer Name -->
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-label">Customer Name</div>
                        <div class="info-value">
                            {{ $veriffData->user->first_name ?? 'N/A' }}
                            {{ $veriffData->user->last_name ?? '' }}
                        </div>
                    </div>
                </div>

                <!-- Customer Email -->
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-label">Customer Email</div>
                        <div class="info-value">
                            {{ $veriffData->user->email ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                <!-- Order ID -->
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-label">Order ID</div>
                        <div class="info-value">
                            #{{ $veriffData->order_id }}
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span
                                class="badge
                                @if ($veriffData->status === 'approved') bg-success
                                @elseif($veriffData->status === 'rejected') bg-danger
                                @else bg-warning text-dark @endif
                            ">
                                {{ ucfirst($veriffData->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Veriff Decision -->
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-label">Veriff Decision</div>
                        <div class="info-value">
                            {{ ucfirst($veriffData->veriff_decision ?? 'N/A') }}
                        </div>
                    </div>
                </div>

                <!-- Verified At -->
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-label">Verified At</div>
                        <div class="info-value">
                            {{ $veriffData->veriff_verified_at
                                ? \Carbon\Carbon::parse($veriffData->veriff_verified_at)->format('d M Y, h:i A')
                                : 'N/A' }}
                        </div>
                    </div>
                </div>

                <!-- Reason (Full Width) -->
                <div class="col-md-12">
                    <div class="info-box">
                        <div class="info-label">Veriff Reason</div>
                        <div class="info-value">
                            {{ $veriffData->veriff_reason ?? 'N/A' }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
