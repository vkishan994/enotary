@extends('front.layouts.common')
@section('css')
    <style>
        /* Container */
        .document-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: auto;
            min-height: 260px;
        }

        /* Specific style for Upcoming card to support scrolling when multiple */
        .upcoming-card {
            max-height: 380px;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        /* Specific style for Notarise card to never show scrollbar */
        .notarise-card {
            overflow: hidden;
            max-height: 380px;
        }

        /* Prevent hover color change for the Notarise button */
        .notarise-card .btn-primary:hover {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #fff !important;
            box-shadow: none !important;
            transform: none !important;
        }




        .document-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }


        /* Support horizontal scroll for tabs */
        .document-pending .nav-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 5px;
            gap: 10px;
            border-bottom: none;
        }

        .document-pending .nav-tabs::-webkit-scrollbar {
            height: 6px;
        }

        .document-pending .nav-tabs::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .document-pending .nav-tabs::-webkit-scrollbar-thumb {
            background: #b8860b;
            border-radius: 10px;
        }

        .document-pending .nav-tabs .nav-item {
            white-space: nowrap;
        }


        .document-card h4 {
            color: #34394c;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #b8860b;
            display: inline-block;
        }

        .document-card p {
            font-size: 15px;
            color: #6b7280;
            line-height: 1.6;
        }



        /* iPad Pro and Tablet Responsive */
        @media (max-width: 1200px) {
            .document-card {
                padding: 14px;
            }

            .document-card h4 {
                font-size: 16px;
                margin-bottom: 12px;
            }

            .document-card p {
                font-size: 14px;
            }

            .document-card .btn {
                padding: 8px 12px;
                font-size: 14px;
            }
        }

        /* iPad Pro (1024px) */
        @media (max-width: 1024px) {
            .document-upcoming .row {
                flex-direction: column;
            }

            .document-upcoming .col-lg-6 {
                width: 100%;
                margin-bottom: 16px;
            }

            .document-card {
                padding: 14px;
            }

            .document-card h4 {
                font-size: 16px;
            }

            .d-flex.flex-column.align-items-end {
                min-width: auto;
                width: 100%;
                align-items: flex-start !important;
            }

            .upcoming-appointment {
                flex-direction: column;
                align-items: flex-start;
            }

            .meeting-note {
                max-width: 100%;
                text-align: left;
            }
        }

        /* Upcoming Appointment Premium Design */
        .upcoming-appointment {
            background: #fdfaf3;
            border: 1px solid #f1e4c8;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .upcoming-appointment:hover {
            box-shadow: 0 4px 12px rgba(184, 134, 11, 0.1);
            transform: translateY(-2px);
        }

        .appointment-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .appointment-badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .badge-item {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge-item i {
            color: #b8860b;
        }

        .service-info {
            font-size: 14px;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .service-info strong {
            color: #1f2937;
            font-weight: 600;
        }

        .meeting-action {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
            min-width: 160px;
        }

        .btn-join {
            background: linear-gradient(135deg, #b8860b 0%, #d4af37 100%);
            color: white !important;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(184, 134, 11, 0.2);
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-join:hover {
            box-shadow: 0 6px 15px rgba(184, 134, 11, 0.3);
            transform: scale(1.02);
        }

        .meeting-note {
            font-size: 12px;
            text-align: right;
            line-height: 1.4;
        }


        /* Colors */
        .text-warning {
            color: #d97706 !important;
        }

        .text-muted {
            color: #6b7280 !important;
        }

        @media (max-width: 1024px) {
            .dashboard-header .menu-toogle {
                position: relative;
            }

            .dashboard-header .menu-toogle img {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
        }

        /* MOBILE FIX */
        @media (max-width: 768px) {

            .upcoming-appointment {
                flex-direction: column;
                align-items: flex-start;
            }

            .d-flex.flex-column.align-items-end {
                align-items: flex-start !important;
                width: 100%;
            }

            .meeting-note {
                text-align: left;
                max-width: 100%;
            }
        }
    </style>
@section('content')

    @include('front.layouts.dashboard.sidebar')

    <!-- Main content start -->
    <main class="main-content">
        <div class="section-title" @if ($orders->count() == 0) style="height: 150px;" @endif>
            <h2>Hello {{ $user->first_name }}</h2>
        </div>
        <div class="document-upcoming">
            <div class="row align-items-stretch">

                <div class="col-lg-6 mb-3">
                    @if ($upcomingMeetings->count() > 0)
                        <div class="document-card upcoming-card">


                            <h4>Upcoming Appointments</h4>
                            @foreach ($upcomingMeetings as $meeting)
                                @php
                                    $meetingDateTime = Carbon\Carbon::createFromFormat(
                                        'Y-m-d H:i:s',
                                        $meeting->meeting_date . ' ' . $meeting->meeting_time,
                                        config('app.timezone'),
                                    );

                                    $linkStartTime = $meetingDateTime->copy()->subHours(3);
                                    $linkEndTime = $meetingDateTime->copy()->addMinutes(30);

                                    $canJoinMeeting = now(config('app.timezone'))->between(
                                        $linkStartTime,
                                        $linkEndTime,
                                    );
                                @endphp


                                <div class="upcoming-appointment">
                                    <div class="appointment-info">
                                        <div class="appointment-badges">
                                            <div class="badge-item">
                                                <i class="fas fa-calendar-alt"></i>
                                                {{ $meetingDateTime->format('F j, Y') }}
                                            </div>
                                            <div class="badge-item">
                                                <i class="fas fa-clock"></i>
                                                {{ $meetingDateTime->format('g:i A') }}
                                            </div>
                                        </div>

                                        <div class="service-info">
                                            <i class="fas fa-file-contract"></i>
                                            <span>Service:</span>
                                            <strong>{{ $meeting->order->document->name ?? 'N/A' }}</strong>
                                        </div>
                                    </div>

                                    <div class="meeting-action">
                                        @if ($canJoinMeeting)
                                            <a class="btn-join" href="{{ $meeting->google_meet_link }}" target="_blank">
                                                <i class="fas fa-video me-1"></i> Join Meeting
                                            </a>
                                            <div class="meeting-note text-warning">
                                                <small>⚠️ Session is active now</small>
                                            </div>
                                        @else
                                            <div class="meeting-note text-muted">
                                                <small>
                                                    <i class="fas fa-info-circle me-1"></i> Link will appear shortly before the meeting
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            @endforeach
                        </div>
                    @else
                       <div class="document-card">
                            <h4>No upcoming appointments</h4>
                            <p>There are currently no sessions booked. </p>
                            <p>Please schedule a new one if needed.</p>
                        </div>
                    @endif

                </div>
                <div class="col-lg-6 mb-3">
                    <div class="document-card notarise-card">
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
                                            <a
                                                href="{{ route('user.verification.page', ['order_id' => encrypt($order->id)]) }}"><i
                                                    class="fas fa-chevron-right"></i></a>
                                        </div>
                                    </div>

                                    @if (isset($order->veriffData) && !empty($order->veriffData) && $order->veriffData->status == 'approved')
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
                                                <a href="{{ route('user.documentList', ['id' => encrypt($order->id)]) }}"><i
                                                        class="fas fa-chevron-right"></i></a>
                                            </div>
                                        </div>
                                    @endif


                                    @if ($order->all_docs_verified)
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
                                                <a
                                                    href="{{ route('user.scheduleMeetingForm', ['order_id' => encrypt($order->id)]) }}"><i
                                                        class="fas fa-chevron-right"></i></a>
                                            </div>
                                        </div>
                                    @endif

                                    @if (isset($order->scheduleMeeting) && !empty($order->scheduleMeeting) && $order->scheduleMeeting->status == 'verified')
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
                                    @endif

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
