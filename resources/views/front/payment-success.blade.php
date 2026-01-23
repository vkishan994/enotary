@extends('front.layouts.common')
@section('content')

@include('front.layouts.dashboard.sidebar')

<!-- Main content start -->
<main class="main-content">
    <div class="section-title text-center mt-5" style="height: 450px;">
        <div class="mb-4">
            <i class="fas fa-check-circle text-success" style="font-size: 80px; "></i>
        </div>
        <h2>Order Successful!</h2>
        <p class="lead mt-3">Thank you for your order. Your document notarisation process is being initiated.</p>
        <div class="mt-4">
            <a href="{{ route('user.account-dashboard') }}" class="btn btn-primary px-4 py-2">Go to Dashboard</a>
        </div>
    </div>
</main>
<!-- Main content end -->

@endsection
