@extends('front.layouts.common')

@section('css')
@endsection

@section('content')
    @include('front.layouts.dashboard.sidebar')

    <!-- Main content start -->
    <main class="main-content">

        <div class="document-upload document-pending">
            {{-- Title --}}
            <div class="section-title">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h4>Notifications</h4>
                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ route('user.account-dashboard') }}" class="btn back-btn">
                            Back
                        </a>
                    </div>
                </div>
            </div>

            {{-- Alerts --}}
            <x-alert type="success" :message="session('success')" />
            <x-alert type="danger" :message="session('error')" />

            {{-- Mark all as read --}}
            @if ($unreadCount > 0)
                <div class="text-end mb-3">
                    <form method="POST" action="{{ route('user.notifications.markAllRead') }}">
                        @csrf
                        <button class="btn btn-primary btn-sm">
                            Mark all as read
                        </button>
                    </form>
                </div>
            @endif

            {{-- Notifications list (NO SCROLLBAR) --}}
            <div class="pending-list mb-4" style="overflow: hidden;height: 500px;overflow-y: auto;">
                @forelse ($notifications as $notification)
                    <div class="pending-item {{ is_null($notification->read_at) ? 'unread-item' : '' }}">
                        <div class="pending-item-content">
                            <div class="pending-text">
                                <h5 class="mb-1">
                                    <i class="fas fa-bell me-1 text-warning"></i>
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </h5>

                                <p class="mb-1 text-muted small">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>

                                @if (isset($notification->data['extra']['meeting_link']) && $notification->data['extra']['meeting_link'])
                                    <div class="mt-1">
                                        <a href="{{ $notification->data['extra']['meeting_link'] }}" target="_blank"
                                            class="btn btn-sm btn-success">
                                            Join Meeting
                                        </a>
                                    </div>
                                @endif

                                <small class="text-muted">
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>

                        {{-- Action --}}
                        @if (is_null($notification->read_at))
                            <div class="notification-action">
                                <form method="POST" action="{{ route('user.notifications.markRead', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary" title="Mark as read">
                                        Mark as read
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="pending-arrow text-muted">
                                <i class="fas fa-envelope-open"></i>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fa fa-bell-slash me-2"></i>
                        <span>No notifications found.</span>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($notifications->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">
                        Showing
                        {{ $notifications->firstItem() ?? 0 }}
                        –
                        {{ $notifications->lastItem() ?? 0 }}
                        of
                        {{ $notifications->total() }}
                        notifications
                    </small>

                    {{ $notifications->links('vendor.pagination.bootstrap-5') }}
                </div>
            @endif
        </div>

    </main>
    <!-- Main content end -->
@endsection
