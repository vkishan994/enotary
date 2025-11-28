<!DOCTYPE html>
<html lang="en" class="">

<head>
    <title>Verify Email | White Horse Solicitors & Notary Public</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="shortcut icon" href="{{ asset('front/img/logo/logo.png') }}" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="{{ asset('front/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <link id="style" rel="stylesheet" type="text/css" href="{{ asset('front/css/style.css') }}" />
    <style>
        .logout-btn  {
            color: #34394c;
            font-size: 18px;
            line-height: 24px;
            font-weight: 400;
            display: block;
            text-align: center;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- Start Verify Section -->
    <section class="signup-section">
        <div class="container-fluid px-0">
            <div class="row align-items-xl-center">

                <!-- Left Image -->
                <div class="col-lg-5 col-md-12 signup-image d-none d-lg-block">
                    <div class="signup-image-wrapper">
                        <img src="{{ asset('front/img/home/sign-up-left-image.png') }}" alt="Signup Image"
                            class="img-fluid" />
                    </div>
                </div>

                <!-- Right Form Section -->
                <div class="col-lg-7 col-md-12 signup-form-section">
                    <div class="signup-form-wrapper">

                        <!-- Logo + Title -->
                        <div class="login-header text-center mb-4">
                            <a href="{{ url('/') }}" class="logo">
                                <img src="{{ asset('front/img/logo/logo.png') }}" alt="Whitehorse Logo" />
                            </a>
                            <h2 class="signup-title mt-3">Trusted Digital Notary</h2>
                        </div>

                        <!-- Card -->
                        <div class="card p-4">

                            <h4 class="text-center mb-3">{{ __('Verify Your Email Address') }}</h4>

                            @if (session('resent'))
                                <div class="alert alert-success" role="alert">
                                    {{ __('A fresh verification link has been sent to your email address.') }}
                                </div>
                            @endif

                            <p class="text-center">
                                {{ __('Before proceeding, please check your email for a verification link.') }}
                                <br>
                                {{ __('If you did not receive the email') }},
                            </p>

                            <form class="text-center" method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary mt-2">
                                    {{ __('Click here to request another') }}
                                </button>
                            </form>

                            <div class="text-center mt-3">
                                <a class="logout-btn" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                                    @csrf
                                </form>
                            </div>

                        </div> <!-- /card -->

                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- End Verify Section -->

    <script src="{{ asset('front/js/jquery.min.js') }}"></script>
    <script src="{{ asset('front/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('front/js/popper.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

</body>
</html>
