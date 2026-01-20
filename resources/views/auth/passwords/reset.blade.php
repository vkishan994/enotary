@extends('auth.layouts.auth_layout')

@section('title', 'Reset Password | White Horse Solicitors & Notary Public')

@section('content')

    <!-- Start Signup Section -->

@section('page-title')
    <p>Reset Password</p>
@endsection

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<form action="{{ route('password.update') }}" method="post" class="signup-form">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">
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

    <div class="form-group" style="position: relative;">
        <input type="password" id="password_confirmation" name="password_confirmation"
            class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm Password"
            required style="padding-right: 40px;" />
        <span onclick="togglePassword('password_confirmation', 'icon1_confirm_password')"
            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
            <i id="icon1_confirm_password" class="fa fa-eye-slash"></i>
        </span>

        @error('password_confirmation')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
    </div>
</form>

<!-- End Signup Section -->

@endsection
