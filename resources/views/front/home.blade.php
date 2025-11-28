@extends('front.layouts.common')
@section('content')
    <!-- Banner Section Start -->
    <section class="banner-section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="banner-content mb-5 mb-lg-0">
                        <h2>Notarise your<br>
                            documents anytime <br>
                            and anywhere</h2>
                        <p>Experience the convenience of secure and professional eNotary
                            services. Whether you’re an individual or a business, we’re here to
                            help you notarise your important documents with ease.</p>
                        <a href="#" class="btn btn-primary">Get Started <i
                                class="fa-solid fa-arrow-right-long ms-2"></i></a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="banner-image text-lg-end text-center">
                        <img src="{{ asset('front/img/home/banner-image.png') }}" alt="Banner Image" />
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Banner Section End -->

    <!-- About Section Start -->
    <section class="about-section pt-80 pb-80">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-image text-lg-end text-center">
                        <img src="{{ asset('front/img/home/about-image.png') }}" alt="About Image" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-content mb-5 mb-lg-0 ms-lg-5">
                        <h2>Our Online<br>
                            Notary Service</h2>
                        <p>Online e-signature notarisation lets you have documents legally notarised through a secure
                            virtual session, removing the need to meet a notary in person.</p>
                        <p>

                            Your identity is confirmed through live video, and the notary directly witnesses your
                            digital signature before applying an official electronic seal.</p>

                        <p> This modern process is fast, convenient, and fully compliant with legal standards, allowing
                            you to complete important documents from anywhere.</p>

                        <a href="#" class="btn btn-primary">Get Started <i
                                class="fa-solid fa-arrow-right-long ms-2"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About Section End -->

    <!-- How It Works Section Start -->
    <section class="how-it-works-section pt-80 pb-80">
        <div class="container-fluid">
            <div class="section-title text-center mb-5">
                <h2>How It Works</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="how-it-works-item p-4">
                        <div class="icon mb-3">
                            <img src="{{ asset('front/img/home/icon1.png') }}" alt="Upload Icon" />
                        </div>
                        <h4>Verify Your Identity</h4>
                        <p>Upload your ID and complete quick security checks.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="how-it-works-item p-4">
                        <div class="icon mb-3">
                            <img src="{{ asset('front/img/home/icon2.png') }}" alt="Upload Icon" />
                        </div>
                        <h4>Meet the Notary Online</h4>
                        <p>Join a live video call, sign your document, and the notary verifies and seals it digitally.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="how-it-works-item p-4">
                        <div class="icon mb-3">
                            <img src="{{ asset('front/img/home/icon3.png') }}" alt="Upload Icon" />
                        </div>
                        <h4>Download Your<br>
                            Notarised Document</h4>
                        <p>Receive your secure, tamper-proof notarised file instantly.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- How It Works Section End -->

    <!-- Testimonials section start -->
    <section class="testimonials-section pt-80 pb-80">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h2>Testimonials</h2>
            </div>
            <div class="testimonial-carousel">
                <div class="testimonial-item">
                    <div class="stars">
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                    </div>
                    <p class="testimonial-text">The e-signature process was incredibly quick and easy. I completed
                        everything online within minutes, and the notarised document was delivered instantly. Highly
                        recommended!
                    </p>
                    <h5 class="testimonial-author">Stephan Bowden
                    </h5>
                </div>
                <div class="testimonial-item">
                    <div class="stars">
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                    </div>
                    <p class="testimonial-text">The e-signature process was incredibly quick and easy. I completed
                        everything online within minutes, and the notarised document was delivered instantly. Highly
                        recommended!
                    </p>
                    <h5 class="testimonial-author">Stephan Bowden
                    </h5>
                </div>
                <div class="testimonial-item">
                    <div class="stars">
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                        <span>★</span>
                    </div>
                    <p class="testimonial-text">The e-signature process was incredibly quick and easy. I completed
                        everything online within minutes, and the notarised document was delivered instantly. Highly
                        recommended!
                    </p>
                    <h5 class="testimonial-author">Stephan Bowden
                    </h5>
                </div>
            </div>
        </div>
    </section>
    <!-- Testimonials section end -->
@endsection
