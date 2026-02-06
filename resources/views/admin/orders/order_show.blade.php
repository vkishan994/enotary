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

        .card-header h5 {
            font-weight: 600;
            margin: 0;
        }

        .card-body {
            padding: 22px;
        }

        /* ===== Order Summary ===== */
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

        /* ===== Documents ===== */
        .document-wrapper {
            background: #ffffff;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            margin-bottom: 20px;
        }

        .document-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .document-title {
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #0d6efd;
        }

        /* ===== File Card ===== */
        .file-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f9fafc;
            border-radius: 14px;
            padding: 14px 16px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 10px;
            transition: 0.2s ease;
        }

        .file-card:hover {
            background: #f1f4fb;
        }

        .file-info {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .file-thumb {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: #fff;
        }

        .file-name {
            font-size: 14px;
            font-weight: 600;
            color: #212529;
            word-break: break-word;
        }

        /* ===== File Actions ===== */
        .file-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }

        .file-actions .btn {
            border-radius: 10px;
        }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .file-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .file-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="page-title mb-0">
            <span class="text-muted">Orders /</span> Order Details
        </h4>

        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bx bx-left-arrow-alt"></i> Back
        </a>
    </div>

    <x-alert type="success" :message="session('success')" />
    <x-alert type="danger" :message="session('error')" />

    <!-- ===== Order Summary ===== -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Order Summary</h5>
        </div>
        <div class="card-body mt-4">
            <div class="summary-grid">
                <div class="summary-box">
                    <div class="summary-label">Request</div>
                    <div class="summary-value">{{ $order->document->name ?? 'N/A' }}</div>
                </div>

                <div class="summary-box">
                    <div class="summary-label">Customer Name</div>
                    <div class="summary-value">{{ $order->user->first_name ?? 'N/A' }}</div>
                </div>

                <div class="summary-box">
                    <div class="summary-label">Payment Status</div>
                    <div class="summary-value">{!! paymentStatus($order->payment_status) !!}</div>
                </div>

                <div class="summary-box">
                    <div class="summary-label">Document Status</div>
                    <div class="summary-value">{!! documentUploadStatus($order->upload_document_status) !!}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Uploaded Documents ===== -->
    <div class="card">
        <div class="card-header">
            <h5>Uploaded Documents</h5>
        </div>
        <div class="card-body mt-4">

            @foreach ($uploadedDocuments as $document)
                <div class="document-wrapper">

                    <div class="document-header">
                        <div class="document-title">
                            <i class="bx bx-file"></i>
                            {{ $document->uploadedDocument->name }}
                        </div>
                    </div>

                    @foreach ($document->verify_document_items ?? [] as $item)
                        @php
                            $fileUrl = asset('storage/' . $item->file_path);
                            $fileName = basename($item->file_path);
                        @endphp

                        <div class="file-card">
                            <div class="file-info">
                                <img src="{{ $fileUrl }}"
                                    onerror="this.src='{{ asset('images/file-placeholder.png') }}'" class="file-thumb">
                                <div class="file-name">{{ $fileName }}</div>
                            </div>

                            <div class="file-actions">
                                <a href="{{ $fileUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bx bx-link-external"></i>
                                </a>

                                <a href="{{ $fileUrl }}" download class="btn btn-outline-secondary btn-sm">
                                    <i class="bx bx-download"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach

                </div>
            @endforeach

        </div>
    </div>
@endsection
