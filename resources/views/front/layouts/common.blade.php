@if (auth()->check())
    @include('front.layouts.dashboard.header')
@else
    @include('front.layouts.header')
@endif
@yield('content')
@include('front.layouts.footer')
