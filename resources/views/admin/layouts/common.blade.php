@include('admin.layouts.header')

{{-- flash messages shown after redirects --}}
@include('admin.layouts.flash')

@yield('content')

@include('admin.layouts.footer')
