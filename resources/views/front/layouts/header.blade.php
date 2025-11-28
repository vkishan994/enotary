<!DOCTYPE html>
<html lang="en" class="">

<head>

    <!-- Site Title -->
    <title>Home | White Horse Solicitors & Notary Public</title>
    <!-- Character Set and Responsive Meta Tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('front/img/logo/logo.png')}}" type="image/x-icon" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('front/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">

    <!-- Custom Styles -->
   <link id="style" rel="stylesheet" type="text/css" href="{{ asset('front/css/style.css')}}" />

   @yield('css')

</head>

<body>

    <!-- Header Section -->
    <header class="header-section">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><img src="{{ asset('front/img/logo/logo.png')}}" alt="" /> </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Home</a>
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
                            <a class="nav-link" href="#"> Contact</a>
                        </li>
                    </ul>
                    <div class="d-inline align-items-center right-btn d-lg-none d-block">
                        <a href="#" class="nav-link">Sign Up</a>
                        <a href="#" class="btn btn-primary">Log In</a>
                    </div>
                </div>
                <div class="align-items-center right-btn d-lg-flex d-none">
                    <a href="{{route('register')}}" class="nav-link">Sign Up</a>
                    <a href="{{route('login')}}" class="btn btn-primary">Log In</a>
                </div>

            </div>
        </nav>
    </header>
    <!-- End Header Section -->
