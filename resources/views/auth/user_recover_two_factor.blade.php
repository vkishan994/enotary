@extends('auth.layouts.auth_layout')

@section('title', 'Recover Two Factor | White Horse Solicitors & Notary Public')

@section('content')

    <!-- Start Signup Section -->

@section('page-title')
    <p>Recover Two Factor Authentication</p>
@endsection

@if (session('error'))
    <div id="error-alert" class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div id="error-alert" class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif
<form method="POST" action="{{ route('user.2fa.recover.send') }}">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
            placeholder="Enter your email or username" autofocus />
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <button class="btn d-grid w-100 mt-3" style="background-color:#b47e0a;color:white">Send
        Recovery Link</button>
</form>

<!-- End Signup Section -->

@endsection
