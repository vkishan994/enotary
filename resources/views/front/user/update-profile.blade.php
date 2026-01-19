@extends('front.layouts.common')
@section('content')
    @include('front.layouts.dashboard.sidebar')
    <!-- Main content start -->
    <main class="main-content">
        <div class="document-pending">
            <div class="section-title">
                <h4>Update Profile Details</h4>
            </div>

            <x-alert type="success" :message="session('success')" />
            <x-alert type="danger" :message="session('error')" />

            <div class="service-notary">
                <div class="form-container">

                    <form method="POST" action="{{ route('user.update-profile') }}">
                        @csrf

                        <h2 class="section-title">Personal Information</h2>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control"
                                    value="{{ old('first_name', auth()->user()->first_name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control"
                                    value="{{ old('last_name', auth()->user()->last_name) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                        </div>

                        {{--  Two-Factor Authentication --}}
                        <h2 class="section-title mt-4">Security</h2>

                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <div>
                                <strong>Two-Factor Authentication (2FA)</strong>
                                <p class="text-muted mb-0">
                                    Add an extra layer of security to your account.
                                </p>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="enable_2fa" value="1"
                                    id="google2fa_status" {{ auth()->user()->google2fa_status ? 'checked' : '' }}>
                            </div>
                        </div>

                        @if (auth()->user()->google2fa_status)
                            <div class="alert alert-success">
                                Two-Factor Authentication is enabled.
                            </div>
                        @else
                            <div class="alert alert-warning">
                                ⚠️ Two-Factor Authentication is disabled.
                            </div>
                        @endif

                        <h2 class="section-title mt-4">Change Password (Optional)</h2>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control">
                                @error('current_password')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control">
                                @error('password')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                                @error('password_confirmation')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="place-order-btn mt-3">
                            Update Profile
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </main>

    @include('partials.two_factor')

    <!-- Main content end -->
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var user2faGenerate = "{{ route('user.2fa.generate') }}";
        var userVerifyTwoFactor = "{{ route('user.2fa.verify') }}";
    </script>
    <script src="{{ asset('common/two_factor.js') }}"></script>

    <script></script>
@endsection
