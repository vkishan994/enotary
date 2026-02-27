<!DOCTYPE html>

<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>
        @hasSection('title')
            @yield('title') | Admin | White Horse Solicitors & Notary Public
        @elseif(isset($title))
            {{ $title }} | Admin | White Horse Solicitors & Notary Public
        @else
            Admin | White Horse Solicitors & Notary Public
        @endif
    </title>

    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('front/img/logo/logo.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/core.css') }}"
        class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">

    <style>
        .nav-item.dropdown-notifications {
            position: relative;
        }

        .notification-count {
            position: absolute !important;
            top: 2px;
            right: -2px;
            font-size: 0.75rem;
            padding: 0.25rem 0.2rem !important;
            min-width: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    @yield('css')

    <!-- Helpers -->
    <script src="{{ asset('admin/assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('admin/assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('admin.layouts.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <i class="bx bx-search fs-4 lh-0"></i>
                                <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2"
                                    placeholder="Search..." aria-label="Search..." />
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">

                            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                                <a class="nav-link dropdown-toggle hide-arrow position-relative"
                                    href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                    aria-expanded="false">

                                    <i class="icon-base bx bx-bell icon-md"></i>

                                    @if ($unreadCount > 0)
                                        <span class="badge bg-danger rounded-pill notification-count">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end p-0">

                                    {{-- Header --}}
                                    <li class="dropdown-menu-header border-bottom">
                                        <div class="dropdown-header d-flex align-items-center py-3">
                                            <h6 class="mb-0 me-auto">Notification</h6>

                                            @if ($unreadCount > 0)
                                                <div class="d-flex align-items-center h6 mb-0">
                                                    <span class="badge bg-label-primary me-2">
                                                        {{ $unreadCount }} New
                                                    </span>

                                                    <form method="POST"
                                                        action="{{ route('notifications.markAllRead') }}"
                                                        class="ms-1">
                                                        @csrf
                                                        <button type="submit"
                                                            class="dropdown-notifications-all p-2 border-0 bg-transparent"
                                                            data-bs-toggle="tooltip" title="Mark all as read">
                                                            <i class="icon-base bx bx-envelope-open text-heading"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </li>

                                    {{-- Notification List --}}
                                    <li class="dropdown-notifications-list scrollable-container">

                                        <ul class="list-group list-group-flush">

                                            @forelse ($notifications as $notification)
                                                <li
                                                    class="list-group-item list-group-item-action dropdown-notifications-item">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <h6 class="small mb-0">
                                                                {{ $notification->data['title'] ?? 'Notification' }}
                                                            </h6>

                                                            <small class="mb-1 d-block text-body">
                                                                {{ $notification->data['message'] ?? '' }}
                                                            </small>

                                                            <small class="text-body-secondary">
                                                                {{ $notification->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>

                                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                                            <form method="POST"
                                                                action="{{ route('notifications.markRead', $notification->id) }}">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="border-0 bg-transparent p-0"
                                                                    title="Mark as read">
                                                                    <i class="bx bx-x fs-5"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </li>
                                            @empty
                                                {{-- No notifications message --}}
                                                <li class="list-group-item text-center py-4">
                                                    <i class="bx bx-bell-off fs-4 text-muted mb-1"></i>
                                                    <p class="mb-0 text-muted small">
                                                        No notifications found
                                                    </p>
                                                </li>
                                            @endforelse

                                        </ul>
                                    </li>

                                    {{-- View All (ONLY if notifications exist) --}}
                                    @if ($notifications->count() > 0)
                                        <li class="border-top">
                                            <div class="d-grid p-3">
                                                <a class="btn btn-primary btn-sm d-flex justify-content-center"
                                                    href="{{ route('admin.notifications.index') }}">
                                                    View all notifications
                                                </a>
                                            </div>
                                        </li>
                                    @endif

                                </ul>

                            </li>

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('admin/assets/img/avatars/user-logo.png') }}" alt
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="{{ asset('admin/assets/img/avatars/user-logo.png') }}"
                                                            alt class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-medium d-block">{{ Auth::user()->name }}</span>
                                                    {{-- <small class="text-muted">Admin</small> --}}
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.edit.profile') }}">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">My Profile</span>
                                        </a>
                                    </li>

                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href=" {{ route('logout') }} ">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Layout Demo -->
