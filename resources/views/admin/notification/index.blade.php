@extends('admin.layouts.common')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">

        {{-- Left: Title + counts --}}
        <h4 class="page-title mb-0 d-flex align-items-center gap-2">
            <span class="text-muted fw-light">Notifications /</span>
            <span>All Notifications</span>

            @if ($unreadCount > 0)
                <span class="badge bg-danger">
                    {{ $unreadCount }} Unread
                </span>
            @endif
        </h4>

        {{-- Right: Mark all as read --}}
        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <button class="btn btn-sm btn-primary">
                    Mark all as read
                </button>
            </form>
        @endif

    </div>


    {{-- Alerts --}}
    <x-alert type="success" :message="session('success')" />
    <x-alert type="danger" :message="session('error')" />

    <div class="row">
        <div class="col-md-12">

            @forelse ($notifications as $notification)
                <div class="card mb-3 {{ is_null($notification->read_at) ? 'border-primary' : '' }}">

                    {{-- Header --}}
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">
                            {{ $notification->data['title'] ?? 'Notification' }}
                        </h6>

                        <small class="text-muted">
                            {{ $notification->created_at->format('d M Y, h:i A') }}
                        </small>
                    </div>

                    {{-- Body --}}
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <p class="mb-2">
                                {{ $notification->data['message'] ?? '-' }}
                            </p>

                            @if (is_null($notification->read_at))
                                <form method="POST" action="{{ route('notifications.markRead', $notification->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success" title="Mark as read">
                                        <i class="bx bx-check"></i>
                                    </button>
                                </form>
                            @endif
                        </div>

                        @if (!empty($notification->data['extra']))
                            <hr>
                            <h6 class="mb-2">Details</h6>

                            <ul class="list-unstyled mb-0">
                                @foreach ($notification->data['extra'] as $key => $value)
                                    <li class="mb-1">
                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                        {{ $value }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-inbox fs-1 text-muted mb-3 d-block"></i>
                        <h6 class="text-muted mb-2">No Notifications Found</h6>
                        <p class="text-muted small mb-0">You're all caught up! There are no notifications at this time.</p>
                    </div>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if($unreadCount > 0)
            <div class="d-flex justify-content-between align-items-center mt-4">
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
    </div>
@endsection
