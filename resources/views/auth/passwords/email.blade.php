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
<form action="{{ route('password.email') }}" method="post" class="signup-form">
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
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Send Password Reset
            Link</button>
    </div>
</form>


<!-- End Signup Section -->

@endsection
