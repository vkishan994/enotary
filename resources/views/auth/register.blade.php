@extends('auth.layouts.auth_layout')

@section('title', 'Sign Up | White Horse Solicitors & Notary Public')

@section('content')

    <!-- Start Signup Section -->

@section('page-title')
    <p>Create a secure account</p>
@endsection
<form actio="{{ route('register') }}" method="post" class="signup-form">
    @csrf
    <div class="form-group">
        <input type="text" id="first_name" name="first_name"
            class="form-control @error('first_name') is-invalid @enderror" placeholder="First Name" required />
        @error('first_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="form-group">
        <input type="text" id="last_name" name="last_name"
            class="form-control @error('last_name') is-invalid @enderror" placeholder="Surname" required />
        @error('last_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="form-group">
        <input type="text" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
            placeholder="Enter your email address" required />
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="form-group" style="position: relative;">
        <input type="password" id="password" name="password"
            class="form-control @error('password') is-invalid @enderror" placeholder="Create your password" required
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
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
            placeholder="Retype password" required style="padding-right: 40px;" />
        <span onclick="togglePassword('password_confirmation', 'icon2')"
            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
            <i id="icon2" class="fa fa-eye-slash"></i>
        </span>
    </div>


    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
    </div>
</form>

<!-- End Signup Section -->

@endsection
