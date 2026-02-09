@extends('front.layouts.common')
@section('css')
    <style>
        .upcoming-appointment {
            padding: 14px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 12px;
            background-color: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
        }

        .appointment-details {
            flex: 1;
            min-width: 0;
        }

        .appointment-time {
            font-size: 14px;
            color: #374151;
            margin-bottom: 6px;
        }

        .appointment-time span {
            color: #6b7280;
            font-weight: 500;
        }

        .meeting-url {
            font-size: 13px;
            color: #374151;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .meeting-url span {
            font-weight: 600;
            margin-right: 4px;
        }

        .meeting-url a {
            color: #2563eb;
            text-decoration: none;
            max-width: 100%;
            display: inline-block;
        }

        .meeting-url a:hover {
            text-decoration: underline;
        }

        .meeting-link-btn {
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            background-color: #2563eb;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            white-space: nowrap;
        }

        .meeting-link-btn:hover {
            background-color: #1e40af;
        }

        .service-type {
            font-size: 13px;
            color: #374151;
            margin-bottom: 6px;
        }

        .service-type span {
            font-weight: 600;
            margin-right: 4px;
        }
    </style>
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
                                        <div class="d-flex flex-column align-items-end" style="min-width:140px;">
                                            <a class="meeting-link-btn" href="{{ $meeting->google_meet_link }}" target="_blank">Join Meeting</a>
                                            <div class="meeting-note text-warning mt-2 text-end">
                                                <small>
                                                    ⚠️ Please join the meeting at the scheduled time. If you do not join, the meeting will be cancelled.
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="meeting-note text-muted mt-2">
                                            <small>
                                                ⏳ The Google Meet link will be displayed here shortly before the scheduled meeting time.
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
