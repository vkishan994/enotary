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
        </div>
    </main>

    <!-- Main content end -->
@endsection
