@extends('front.layouts.common')

@section('content')
    @include('front.layouts.dashboard.sidebar')

    <!-- Main content start -->
    <main class="main-content">
        <div class="document-pending">

            <div class="section-title">
                <div class="row">
                    <div class="col-6">
                        <h4>Identity Verification</h4>

                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ route('user.account-dashboard') }}" class="btn back-btn">Back <a>
                    </div>
                    <div class="col-12">
                        <p class="text-muted mt-1 mb-4">
                            To continue with notarization, please verify your identity.
                            This process takes 2–3 minutes.
                        </p>
                    </div>
                </div>


            </div>

            <x-alert type="success" :message="session('success')" />
            <x-alert type="danger" :message="session('error')" />

            <div class="service-notary">
                <div class="form-container">

                    {{-- Verification Card --}}
                    <div class="card p-4 shadow-sm">
                        <div class="d-flex align-items-start gap-3" style="overflow: hidden;height: 200px;overflow-y: auto;">

                            {{-- Icon --}}
                            <div>
                                <i class="fa fa-id-card fa-2x text-primary"></i>
                            </div>

                            {{-- Content --}}
                            <div class="flex-grow-1">
                                <h5 class="mb-2">Verify your identity</h5>

                                <p class="text-muted mb-3">
                                    Your data is encrypted and processed securely.
                                </p>

                                {{-- Status --}}
                                @php
                                    $status = $VeriffData->status ?? 'not_started';
                                @endphp

                                {!! veriffStatus($VeriffData->status ?? null) !!}
                            </div>
                        </div>

                        {{-- Action --}}
                        <div class="mt-4 {{ $status === 'approved' ? 'text-start' : 'text-end' }}">
                            @if ($status === 'approved')
                                <div class="alert alert-success mb-0">
                                    <i class="fa fa-check-circle me-1"></i>
                                    You have successfully verified your identity.
                                </div>
                            @elseif ($status === 'started')
                                <form method="POST" action="{{ route('user.veriff.start', ['order_id' => $order_id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-play me-1"></i> Start Verification
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('user.veriff.start', ['order_id' => $order_id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-play me-1"></i> Start Verification
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Info box --}}
                    @if ($status != 'approved')
                        <div class="alert alert-info mt-4">
                            <strong>What you’ll need to complete verification:</strong>
                            <ul class="mb-0 mt-2">
                                <li>A valid government-issued ID (passport, driving licence, or national ID)</li>
                                <li>
                                    A device with a working camera
                                    <br>
                                    <small class="text-muted">
                                        (Mobile phone recommended. If you’re on a desktop without a camera, please open the
                                        link
                                        on your phone.)
                                    </small>
                                </li>
                                <li>A stable internet connection</li>
                            </ul>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </main>

    @include('partials.two_factor')
@endsection

@section('js')
@endsection
