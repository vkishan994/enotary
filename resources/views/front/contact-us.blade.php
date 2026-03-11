@extends('front.layouts.common')

@section('css')
    <link rel="stylesheet" href="{{ asset('front/css/contact-us.css') }}">
@endsection

@section('content')
    <div class="main-content">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <x-alert type="success" :message="session('success')" />
                    <x-alert type="danger" :message="session('error')" />

                    <div class="card contact-card border-0">
                        <div class="card-body p-4">

                            <h4 class="contact-title mb-4">
                                Send us a Message
                            </h4>

                            <form class="contact-form" method="POST" action="{{ route('contact.store') }}">
                                @csrf

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Enter your name" value="{{ old('name') }}">
                                        @error('name')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control"
                                            placeholder="Enter your email" value="{{ old('email') }}">
                                        @error('email')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control"
                                            placeholder="Enter phone number" value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Subject</label>
                                        <input type="text" name="subject" class="form-control"
                                            placeholder="Enter subject" value="{{ old('subject') }}">
                                        @error('subject')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Message</label>
                                        <textarea name="message" rows="5" class="form-control" placeholder="Write your message">{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mt-3">
                                        <button type="submit" class="btn contact-btn text-white">
                                            Send Message
                                        </button>
                                    </div>

                                </div>

                            </form>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
