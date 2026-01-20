@extends('auth.layouts.auth_layout')

@section('title', 'Verify Email | White Horse Solicitors & Notary Public')

@section('content')



@section('page-title')
    <p>Verify Email</p>
@endsection

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

<!-- End Verify Section -->

@endsection
