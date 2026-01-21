@extends('front.layouts.common')
@section('content')

    @include('front.layouts.dashboard.sidebar')

    <!-- Main content start -->
    <main class="main-content">
        <div class="section-title">
            <h2>Hello {{ $user->first_name }}</h2>
        </div>
        <div class="document-upcoming">
            <div class="row">
                <div class="col-lg-6">
                    <div class="document-card">
                        <h4>No upcoming appointments</h4>
                        <p>There are currently no sessions booked. </p>
                        <p>Please schedule a new one if needed.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="document-card">
                        <h4>Notarise a new document</h4>
                        <p>Notarise your document online and<br> schedule a video appointment.</p>
                        <a href="{{ route('user.notarise-documents') }}" class="btn btn-primary w-100 py-2 mt-2">Notarise a
                            new document +</a>
                    </div>
                </div>
            </div>
        </div>

        @if (isset($orders) && $orders->count() > 0)
            <div class="document-pending">
                <div class="section-title">
                    <h4>Pending Documents for Notarisation</h4>
                </div>

                <ul class="nav nav-tabs" id="notarisationTabs" role="tablist">
                    @php $first = true; @endphp

                    @foreach ($orders as $order)
                        @if ($order->document)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $first ? 'active' : '' }}" id="document-tab-{{ $order->id }}"
                                    data-bs-toggle="tab" data-bs-target="#document-{{ $order->id }}" type="button"
                                    role="tab" aria-controls="document-{{ $order->id }}"
                                    aria-selected="{{ $first ? 'true' : 'false' }}">
                                    {{ $order->document->name }}
                                </button>
                            </li>

                            @php $first = false; @endphp
                        @endif
                    @endforeach
                </ul>


                <div class="tab-content" id="notarisationTabContent">
                    @php $first = true; @endphp

                    @foreach ($orders as $order)
                        @if ($order->document)
                            <div class="tab-pane fade {{ $first ? 'show active' : '' }}" id="document-{{ $order->id }}"
                                role="tabpanel" aria-labelledby="document-tab-{{ $order->id }}">

                                <!-- EXISTING HTML (UNCHANGED) -->
                                <div class="pending-list mb-4">
                                    <div class="pending-item">
                                        <div class="pending-item-content">
                                            <div class="pending-icon">
                                                <img src="{{ asset('front/img/home/icon4.png') }}" alt="" />
                                            </div>
                                            <div class="pending-text">
                                                <h5>Verify your identity</h5>
                                                <p>Confirm your identity in just a few minutes to continue with your
                                                    notarisation.</p>
                                            </div>
                                        </div>
                                        <div class="pending-arrow">
                                            <a href="#"><i class="fas fa-chevron-right"></i></a>
                                        </div>
                                    </div>

                                    <div class="pending-item">
                                        <div class="pending-item-content">
                                            <div class="pending-icon">
                                                <img src="{{ asset('front/img/home/icon5.png') }}" alt="" />
                                            </div>
                                            <div class="pending-text">
                                                <h5>Upload your document</h5>
                                                <p>Upload your document to begin the notarisation process.</p>
                                            </div>
                                        </div>
                                        <div class="pending-arrow">
                                            <a href="{{ route('user.documentList',['id' => encrypt($order->id)])}}"><i class="fas fa-chevron-right"></i></a>
                                        </div>
                                    </div>

                                    <div class="pending-item">
                                        <div class="pending-item-content">
                                            <div class="pending-icon">
                                                <img src="{{ asset('front/img/home/icon6.png') }}" alt="" />
                                            </div>
                                            <div class="pending-text">
                                                <h5>Schedule a video call meeting</h5>
                                                <p>Schedule your video appointment to complete your notarisation.</p>
                                            </div>
                                        </div>
                                        <div class="pending-arrow">
                                            <a href="#"><i class="fas fa-chevron-right"></i></a>
                                        </div>
                                    </div>

                                    <div class="pending-item">
                                        <div class="pending-item-content">
                                            <div class="pending-icon">
                                                <img src="{{ asset('front/img/home/icon7.png') }}" alt="" />
                                            </div>
                                            <div class="pending-text">
                                                <h5>Download your notarised documents</h5>
                                                <p>Download your officially notarised documents here.</p>
                                            </div>
                                        </div>
                                        <div class="pending-arrow">
                                            <a href="#"><i class="fas fa-chevron-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <!-- END EXISTING HTML -->

                            </div>

                            @php $first = false; @endphp
                        @endif
                    @endforeach
                </div>
                
            </div>
        @endif
    </main>
    <!-- Main content end -->


@endsection
