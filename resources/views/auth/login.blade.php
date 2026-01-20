@extends('auth.layouts.auth_layout')

@section('title', 'Login | White Horse Solicitors & Notary Public')

@section('content')

    <!-- Start Signup Section -->

@section('page-title')
    <p>New to Whitehorse? <a href="{{ route('register') }}">Sign up here</a></p>
@endsection
<form action="{{ route('login') }}" method="post" class="signup-form">
    @csrf
    <div class="form-group">
        <input type="text" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
            placeholder="Your email address" required />
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="form-group" style="position: relative;">
        <input type="password" id="password" name="password"
            class="form-control @error('password') is-invalid @enderror" placeholder="Your password" required
            style="padding-right: 40px;" />
        <span onclick="togglePassword('password', 'icon1')"
            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
            <i id="icon1" class="fa fa-eye-slash"></i>
        </span>

        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Log In</button>
    </div>

    <div class="forget-password">
        <a href="{{ route('password.request') }}">Forgot Password?</a>
    </div>
</form>

<!-- End Signup Section -->

@endsection
