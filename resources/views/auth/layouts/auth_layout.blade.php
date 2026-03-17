<!DOCTYPE html>
<html lang="en" class="">

<head>

    <!-- Site Title -->
    {{-- <title>Login | White Horse Solicitors & Notary Public</title> --}}
    <title>
        @yield('title', 'White Horse Solicitors & Notary Public')
    </title>

    <!-- Character Set and Responsive Meta Tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

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


                <div class="col-lg-7 col-md-12 signup-form-section">
                    <div class="signup-form-wrapper">
                        <div class="login-header">
                            <a href="{{ route('fronthomepage')}}" class="logo">
                                <img src="{{ asset('front/img/logo/logo.png') }}" alt="Whitehorse Logo" />
                            </a>
                            <h2 class="signup-title">Trusted Digital Notary</h2>
                            @yield('page-title')
                            {{-- <p>New to Whitehorse? <a href="{{ route('register') }}">Sign up here</a></p> --}}
                        </div>

                        @yield('content')

                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- End Verify Section -->


    <!-- JavaScript Library -->
    <script src="{{ asset('front/js/jquery.min.js') }}"></script>

    <!-- Popper JS and Bootstrap JS -->

    <script src="{{ asset('front/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('front/js/popper.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="{{ asset('front/js/custom.js') }}"></script>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa fa-eye';
            } else {
                input.type = 'password';
                icon.className = 'fa fa-eye-slash';
            }
        }
    </script>


</body>

</html>
