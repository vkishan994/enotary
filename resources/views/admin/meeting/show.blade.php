@extends('admin.layouts.common')
@section('css')
    <style>
        /* ===== Global ===== */
        body {
            background-color: #f5f7fb;
        }

        .page-title {
            font-weight: 600;
            font-size: 20px;
        }

        /* ===== Cards ===== */
        .card {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            padding: 18px 22px;
        }

        .card-header h5,
        .card-header h6 {
            font-weight: 600;
            margin: 0;
        }

        .card-body {
            padding: 22px;
        }

        /* ===== Meeting Summary ===== */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .summary-box {
            background: #f8f9fc;
            border-radius: 14px;
            padding: 16px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .summary-label {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .summary-value {
            font-weight: 600;
            font-size: 15px;
            color: #212529;
        }

        .meeting-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .meeting-link:hover {
            text-decoration: underline;
        }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="page-title mb-0">
            <span class="text-muted">Meeting /</span> Information
        </h4>

        <a href="{{ route('admin.schedule.meetings.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bx bx-left-arrow-alt"></i> Back
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5>Meeting Information</h5>
        </div>

        <div class="card-body mt-4">
            <div class="summary-grid">

                <div class="summary-box">
                    <div class="summary-label">Enotary Service</div>
                    <div class="summary-value">{{ $meeting->order->document->name ?? 'N/A' }}</div>
                </div>

                <div class="summary-box">
                    <div class="summary-label">Status</div>
                    <div class="summary-value">{!! meetingStatus($meeting->status) !!}</div>
                </div>

                <div class="summary-box">
                    <div class="summary-label">Meeting Date & Time</div>
                    <div class="summary-value">
                        <strong>{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('F j, Y') }}</strong>
                        <span> at {{ \Carbon\Carbon::parse($meeting->meeting_time)->format('g:i A') }}</span>
                    </div>
                </div>

                <div class="summary-box">
                    <div class="summary-label">Meeting Mode</div>
                    <div class="summary-value">Online (Google Meet)</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    @endpush
@endsection
