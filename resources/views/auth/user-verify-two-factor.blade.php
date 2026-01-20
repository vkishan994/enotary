@extends('auth.layouts.auth_layout')

@section('title', 'Verify Two Factor | White Horse Solicitors & Notary Public')

@section('content')

    <!-- Start Signup Section -->

@section('page-title')
    <p>Validate Two Factor Authentication</p>
@endsection

@if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first('otp') }}
    </div>
@endif
<form action="{{ route('user.2fa.verify') }}" method="POST">
    @csrf

    <div class="text-center mb-3">
        <p class="fw-bold">Enter your 6-digit authentication code</p>
        <p class="text-muted">Open your Authenticator app</p>
    </div>

    <div class="form-group mb-3">
        <input type="text" name="otp" class="form-control text-center" maxlength="6" placeholder="123456" required
            autofocus>
    </div>

    <button class="btn d-grid w-100 mt-3" style="background-color:#b47e0a;color:white">
        Verify & Login
    </button>

    <div class="text-center mt-3 lost_authenticator">
        <a href="{{ route('user.2fa.recover') }}">
            Lost access to authenticator?
        </a>
    </div>
</form>

<!-- End Signup Section -->

@endsection
