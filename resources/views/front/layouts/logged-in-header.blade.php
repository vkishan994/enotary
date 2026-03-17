<!DOCTYPE html>
<html lang="en" class="">

<head>

    <!-- Site Title -->
    <title>Home | White Horse Solicitors & Notary Public</title>
    <!-- Character Set and Responsive Meta Tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('front/img/logo/logo.png') }}" type="image/x-icon" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('front/css/bootstrap.min.css') }}" />
    {{-- <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css"> --}}
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">

    <!-- Custom Styles -->
    <link id="style" rel="stylesheet" type="text/css" href="{{ asset('front/css/style.css') }}" />
    <style>
        .profile a.username {
            font-size: 20px;
            line-height: 20px;
            font-weight: bolder;
            color: #34394c;
        }
    </style>

    @yield('css')

</head>

<body>

    <!-- Header Section -->
    <header class="header-section">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('fronthomepage') }}"><img
                        src="{{ asset('front/img/logo/logo.png') }}" alt="" /> </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{ route('fronthomepage') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">About</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                eNotary Services
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Services 1</a></li>
                                <li><a class="dropdown-item" href="#">Services 2</a></li>
                                <li><a class="dropdown-item" href="#">Services 3</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"> How it works</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('contact-us') }}"> Contact</a>
                        </li>
                    </ul>
                    <div class="d-inline align-items-center right-btn d-lg-none d-block">
                        <a href="#" class="nav-link">Sign Up</a>
                        <a href="#" class="btn btn-primary">Log In</a>
                    </div>
                </div>
                <div class="align-items-center right-btn d-lg-flex d-none">

                    @auth

                        <!-- Notification Bell -->
                        <div class="dropdown notification-dropdown me-3">
                            <a href="#" class="notification-icon" data-bs-toggle="dropdown">
                                <i class="fa fa-bell"></i>

                                @if (isset($unreadCount) && $unreadCount > 0)
                                    <span class="notification-badge">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </a>

                            <div class="dropdown-menu dropdown-menu-end notification-menu">

                                <div class="notification-header">
                                    <strong>Notifications</strong>
                                </div>

                                @forelse($notifications ?? [] as $notification)
                                    <div class="dropdown-item">
                                        <strong>{{ $notification->data['title'] ?? '' }}</strong>
                                        <p class="mb-0">{{ $notification->data['message'] ?? '' }}</p>
                                    </div>

                                @empty
                                    <div class="dropdown-item text-center">
                                        No notifications
                                    </div>
                                @endforelse

                            </div>
                        </div>


                        <!-- Profile Dropdown -->
                        <div class="profile dropdown">
                            <a href="#" class="d-flex align-items-center username text-decoration-none"
                                data-bs-toggle="dropdown">
                                <span>
                                    {{ auth()->user()->first_name ?? '' }}
                                    {{ auth()->user()->last_name ?? '' }}
                                </span>

                                <img src="{{ asset('front/img/home/down-icon.png') }}" class="rounded-circle mx-2">
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow">

                                <li>
                                    <div class="dropdown-header">
                                        <h6 class="mb-0">
                                            {{ auth()->user()->first_name ?? '' }}
                                            {{ auth()->user()->last_name ?? '' }}
                                        </h6>

                                        <small class="text-muted">
                                            {{ auth()->user()->email ?? '' }}
                                        </small>
                                    </div>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('user.update-profile.user-form') }}">
                                        My Profile
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                                        Logout
                                    </a>
                                </li>

                            </ul>
                        </div>

                    @endauth


                    @guest
                        <a href="{{ route('register') }}" class="nav-link">Sign Up</a>
                        <a href="{{ route('login') }}" class="btn btn-primary">Log In</a>
                    @endguest

                </div>

            </div>
        </nav>
    </header>
    <!-- End Header Section -->
