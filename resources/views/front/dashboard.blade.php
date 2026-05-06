@extends('front.layouts.common')
@section('css')
    <style>
        /* Container */
        .document-card {
            background: #fff;
            border-radius: 10px;
            padding: 16px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* Title */
        .document-card h4 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #1f2937;
        }

        /* Remove scrollbar from document-card and children */
        .document-card,
        .document-card * {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .document-card::-webkit-scrollbar,
        .document-card *::-webkit-scrollbar {
            display: none;
        }

        /* Ensure button fits properly */
        .document-card .btn {
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        /* Appointment row */
        .upcoming-appointment {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fafafa;
            margin-bottom: 12px;
            gap: 16px;
        }

        /* LEFT SIDE */
        .appointment-details {
            flex: 1;
        }

        /* Date + time in one line (FIXED ISSUE) */
        .appointment-time {
            font-size: 14px;
            color: #111827;
            margin-bottom: 4px;
        }

        .appointment-time strong {
            font-weight: 600;
        }

        .appointment-time span {
            color: #6b7280;
            margin-left: 6px;
        }

        /* Service */
        .service-type {
            font-size: 13px;
            color: #4b5563;
        }

        .service-type span {
            font-weight: 500;
            color: #6b7280;
        }

        /* RIGHT SIDE */
        .d-flex.flex-column.align-items-end {
            align-items: flex-end;
            justify-content: center;
            min-width: 150px;
        }

        /* Button */
        .meeting-link-btn {
            font-size: 13px;
            font-weight: 500;
            color: #fff;
            background-color: #2563eb;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
        }

        .meeting-link-btn:hover {
            background-color: #1e40af;
        }

        /* Warning text (FIXED ALIGNMENT) */
        .meeting-note {
            font-size: 12px;
            margin-top: 6px;
            max-width: 220px;
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
            <div class="row">
                <div class="col-lg-6">
                    @if ($upcomingMeetings->count() > 0)
                        <div class="document-card">
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
                                    <div class="appointment-details">
                                        <div class="appointment-time">
                                            <strong>
                                                {{ $meetingDateTime->format('F j, Y') }}
                                            </strong>
                                            <span>
                                                at {{ $meetingDateTime->format('g:i A') }}
                                            </span>
                                        </div>

                                        <div class="service-type">
                                            <span>ENotary Service:</span>
                                            <strong>{{ $meeting->order->document->name ?? 'N/A' }}</strong>
                                        </div>


                                    </div>

                                    @if ($canJoinMeeting)
                                        <div class="d-flex flex-column align-items-start" style="min-width:140px;">
                                            <a class="meeting-link-btn" href="{{ $meeting->google_meet_link }}"
                                                target="_blank">Join Meeting</a>
                                            <div class="meeting-note text-warning mt-2 text-start">
                                                <small>
                                                    ⚠️ Please join the meeting at the scheduled time.
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="meeting-note text-muted mt-2">
                                            <small>
                                                ⏳ The Google Meet link will be displayed here shortly before the scheduled
                                                meeting time.
                                            </small>
                                        </div>
                                    @endif
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
