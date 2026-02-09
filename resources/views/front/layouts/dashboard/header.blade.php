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
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">

    <!-- Custom Styles -->
    <link id="style" rel="stylesheet" type="text/css" href="{{ asset('front/css/style.css') }}" />

    @yield('css')

</head>

<body>

    <!-- dashboard header start -->
    <section class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="logo-sec">
                        <a href="#"><img src="{{ asset('front/img/logo/logo.png') }}" alt="" /> </a>
                    </div>
                </div>
                <div class="col-6">
                    <a href="#" data-bs-toggle="dropdown">
                        🔔
                        <span class="badge bg-danger">1</span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="" class="dropdown-item">
                            <strong>aaa</strong><br>
                            <small>message</small><br>
                            <small class="text-muted">
                            </small>
                        </a>
                        <span class="dropdown-item text-muted">No notifications</span>
                    </div>
                    <div class="d-flex justify-content-end">

                        <div class="profile dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <span> {{ auth()->user()?->first_name ?? '' }}
                                    {{ auth()->user()?->last_name ?? '' }}</span>
                                <img src="{{ asset('front/img/home/down-icon.png') }}" alt="Profile"
                                    class="rounded-circle mx-2">
                            </a>


                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <div class="dropdown-header">

                                        <h6 class="mb-0"> {{ auth()->user()?->first_name ?? '' }}
                                            {{ auth()->user()?->last_name ?? '' }}
                                        </h6>
                                        <small class="text-muted">{{ auth()->user()?->email ?? '' }} </small>
                                    </div>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.update-profile.user-form') }}">
                                        <i class="bi bi-person me-2"></i> My Profile
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="menu-toogle">
                            <img src="{{ asset('front/img/home/menu-toogle.svg') }}" alt="" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- dashboard header end -->
