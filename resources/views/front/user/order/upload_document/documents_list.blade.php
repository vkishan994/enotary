@extends('front.layouts.common')
@section('content')
    @include('front.layouts.dashboard.sidebar')

    <!-- Main content start -->
    <main class="main-content">

        <div class="document-upload document-pending">
            <div class="section-title">
                <div class="row">
                    <div class="col-6">
                        <h4>Upload Documents</h4>
                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ route('user.account-dashboard') }}" class="btn back-btn">Back <a>
                    </div>
                </div>
            </div>


            @if (isset($uploadDocuments) && !empty($uploadDocuments))
                <div class="pending-list mb-4" style="overflow: hidden;height: 500px;overflow-y: auto;">
                    <!-- Document 1 -->
                    @foreach ($uploadDocuments as $uploadDocument)
                        <div class="pending-item">
                            <div class="pending-item-content">
                                <div class="pending-text">
                                    <h5>{{ $uploadDocument->name }}</h5>
                                </div>
                            </div>
                            <div class="pending-arrow">
                                <a
                                    href="{{ route('user.uploadDocument', ['order_id' => encrypt($order_id), 'document_id' => encrypt($uploadDocument->pivot->document_id), 'upload_document_id' => encrypt($uploadDocument->pivot->upload_documents_id)]) }}"><i
                                        class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if (isset($allUploaded) && $allUploaded === true)
                <div class="alert alert-success d-flex justify-content-between align-items-center">
                    <span>
                        All required documents have been uploaded successfully.
                        You may now submit them for verification.
                    </span>

                    <form action="{{ route('user.submitDocumentForVerification') }}" method="POST" class="ms-3">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ encrypt($order_id) }}">
                        <button type="submit" name="submit" class="btn btn-success">
                            <i class="fa fa-check-circle me-1"></i>
                            Submit for Verification
                        </button>
                    </form>
                </div>
            @elseif(isset($alreadySubmitted) &&
                    $alreadySubmitted === true &&
                    (!isset($rejectedDocuments) || $rejectedDocuments->isEmpty()))
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fa fa-clock me-2"></i>
                    <span>
                        Your documents have submitted for verification and are currently under review
                    </span>
                </div>
            @elseif(isset($rejectedDocuments) && $rejectedDocuments->isNotEmpty())
                <div class="alert alert-danger">
                    <h5 class="mb-2">
                        <i class="fa fa-exclamation-circle me-2"></i>
                        Some of your documents were rejected
                    </h5>

                    <p class="mb-2">
                        Please carefully review the admin comments below.
                        Upload the corrected or missing documents as requested,
                        and then submit them again for verification.
                    </p>

                    <p class="mb-3 fw-semibold">
                        ⚠️ Do not submit again until all requested changes are completed.
                    </p>

                    <ul class="list-group mb-3">
                        @foreach ($rejectedDocuments as $rejectedDocument)
                            <li class="list-group-item">
                                <strong>{{ $rejectedDocument->uploadedDocument->name }}</strong>

                                <ul class="mt-2">
                                    <li>
                                        {{ $rejectedDocument->note ?? 'Please follow the admin instructions for this document.' }}
                                    </li>
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <form action="{{ route('user.submitDocumentForVerification') }}" method="POST" class="ms-3">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ encrypt($order_id) }}">

                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check-circle me-1"></i>
                        Submit for Verification
                    </button>
                </form>
            @else
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    <span>
                        Please upload all required documents for verification.
                    </span>
                </div>
            @endif
        </div>
    </main>

    <!-- Main content end -->
@endsection
