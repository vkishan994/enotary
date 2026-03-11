@if (auth()->check() && !request()->is('admin*') && !request()->is('user*'))
    @include('front.layouts.logged-in-header')
@elseif (auth()->check())
    @include('front.layouts.dashboard.header')
@else
    @include('front.layouts.header')
@endif
@yield('content')
@include('front.layouts.footer')
