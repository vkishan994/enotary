@extends('admin.layouts.common')
@section('title', 'Order - Detail')

@section('css')
    <style>
        .document-group {
            background: #fff;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .document-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 14px;
            color: #212529;
        }

        .file-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 10px;
        }

        .file-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .file-thumb {
            width: 54px;
            height: 54px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .file-name {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .file-actions {
            display: flex;
            gap: 8px;
        }

        .file-status-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .rejection-note textarea {
            border-radius: 8px;
        }

        .doc-status-form textarea {
            resize: none;
            min-width: 260px;
        }

        .rejection-note {
            max-width: 420px;
        }

        .btn-success {
            color: #fff;
            background-color: #696cff;
            border-color: #696cff;
            box-shadow: 0 0.125rem 0.25rem 0 rgba(113, 221, 55, 0.4);
        }

        @media (max-width: 768px) {
            .rejection-note {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="page-title mb-0">
            <span class="text-muted fw-light">Orders /</span> Order Details
        </h4>

        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bx bx-left-arrow-alt"></i> Back
        </a>
    </div>

    <x-alert type="success" :message="session('success')" />
    <x-alert type="danger" :message="session('error')" />

    <div class="row">
        <div class="col-md-12">

            <!-- Order Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Notarization</h5>
                </div>
                <div class="card-body">
                    <p><strong>Request:</strong> {{ isset($order->document->name) ? $order->document->name : 'N/A' }}</p>
                    <p><strong>Name:</strong> {{ isset($order->user->first_name) ? $order->user->first_name : 'N/A' }}</p>
                    <p><strong>Payment Status:</strong> {!! paymentStatus($order->payment_status) !!}</p>

                    <p><strong>Document Status:</strong> {!! documentUploadStatus($order->upload_document_status) !!} </p>
                </div>
            </div>

            <!-- Order Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Uploaded Documents</h5>
                </div>
                <div class="card-body">

                    @foreach ($uploadedDocuments as $document)
                        <div class="document-group mb-4">

                            <!-- Document Title -->
                            <h5 class="document-title">
                                {{ $document->uploadedDocument->name }}
                            </h5>

                            <!-- Uploaded Files -->
                            @foreach ($document->verify_document_items ?? [] as $item)
                                @php
                                    $fileUrl = asset('storage/' . $item->file_path);
                                    $fileName = basename($item->file_path);
                                @endphp

                                <div class="file-row">

                                    <!-- Left -->
                                    <div class="file-left">
                                        <img src="{{ $fileUrl }}" class="file-thumb">

                                        <div>
                                            <div class="file-name">{{ $fileName }}</div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="file-actions">
                                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-outline-primary btn-sm"
                                            title="Open in new tab">
                                            <i class="bx bx-link-external"></i>
                                        </a>

                                        <a href="{{ $fileUrl }}" download class="btn btn-outline-secondary btn-sm"
                                            title="Download">
                                            <i class="bx bx-download"></i>
                                        </a>
                                    </div>

                                </div>
                            @endforeach

                            <!-- Document Status (ONE time) -->

                            <div class="document-status-box mt-3">

                                <form method="POST"
                                    action="{{ route('admin.change.doc.status', ['id' => $document->id]) }}"
                                    class="doc-status-form mt-3">
                                    @csrf

                                    <div class="d-flex align-items-start gap-2 flex-wrap">

                                        <!-- Status Dropdown -->
                                        <select name="status" class="form-select form-select-sm w-auto status-select"
                                            data-doc-id="{{ $document->id }}">
                                            <option value="">Select Status</option>
                                            <option value="verified" @if ($document->status == 'verified') selected @endif>
                                                Verified</option>
                                            <option value="rejected" @if ($document->status == 'rejected') selected @endif>
                                                Rejected</option>
                                        </select>

                                        @if ($document->status == 'rejected' && !empty($document->note))
                                            <div class="rejection-note flex-grow-1" id="rejection-note-{{ $document->id }}"
                                                style="display:block;">
                                                <textarea name="rejection_note" class="form-control form-control-sm" rows="2"
                                                    placeholder="Enter rejection reason...">{{ $document->note }}</textarea>
                                            </div>
                                        @else
                                            <!-- Rejection Note -->
                                            <div class="rejection-note flex-grow-1" id="rejection-note-{{ $document->id }}"
                                                style="display:none;">
                                                <textarea name="rejection_note" class="form-control form-control-sm" rows="2"
                                                    placeholder="Enter rejection reason..."></textarea>
                                            </div>
                                        @endif

                                        <!-- Save Button -->
                                        <button type="submit" class="btn btn-success btn-sm px-3">
                                            Save
                                        </button>

                                    </div>
                                </form>

                            </div>

                        </div>
                    @endforeach

                </div>

            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.status-select').forEach(function(select) {
                select.addEventListener('change', function() {

                    const docId = this.dataset.docId;
                    if (!docId) return;

                    const noteDiv = document.getElementById('rejection-note-' + docId);
                    if (!noteDiv) return;

                    if (this.value === 'rejected') {
                        noteDiv.style.display = 'block';
                        const textarea = noteDiv.querySelector('textarea');
                        if (textarea) textarea.focus();
                    } else {
                        noteDiv.style.display = 'none';
                    }
                });
            });
        </script>
    @endpush
@endsection
