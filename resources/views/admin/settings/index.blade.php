@extends('admin.layouts.common')
@section('title', 'Settings')
@section('css')
    <style>
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff !important;
            /* border: 1px solid transparent; */
            border-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-image-slice: 1;
        }

        .nav-tabs .nav-link:hover {
            border-color: #667eea;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Settings </h5>
                </div>

                <x-alert type="success" :message="session('success')" />
                <x-alert type="danger" :message="session('error')" />
                <div class="card-body">

                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                                type="button" role="tab">
                                General Settings
                            </button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link" id="stripe-tab" data-bs-toggle="tab" data-bs-target="#stripe"
                                type="button" role="tab">
                                Stripe Payment
                            </button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link" id="google-tab" data-bs-toggle="tab" data-bs-target="#google"
                                type="button" role="tab">
                                Google OAuth
                            </button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link" id="veriff-tab" data-bs-toggle="tab" data-bs-target="#veriff"
                                type="button" role="tab">
                                Veriff Settings
                            </button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link" id="docusign-tab" data-bs-toggle="tab" data-bs-target="#docusign"
                                type="button" role="tab">
                                DocuSign Settings
                            </button>
                        </li>

                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">

                        <!-- General Settings -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">

                            <form action="{{ route('admin.settings.store') }}" method="post">
                                @csrf
                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Admin Get Email</label>
                                        <input type="hidden" name="module_name" value="general_settings">
                                        <input type="email" name="admin_email" class="form-control"
                                            placeholder="admin@example.com"
                                            value="{{ getValuesByKey('admin_email') ?? '' }}">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Save General Settings</button>
                            </form>
                        </div>

                        <!-- Stripe Settings -->
                        <div class="tab-pane fade" id="stripe" role="tabpanel">

                            <form action="{{ route('admin.settings.store') }}" method="post">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="module_name" value="stripe_payment">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Stripe Public Key</label>
                                        <input type="text" name="stripe_public_key" class="form-control"
                                            placeholder="pk_live_..."
                                            value="{{ getValuesByKey('stripe_public_key') ?? '' }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Stripe Secret Key</label>
                                        <input type="text" name="stripe_secret_key" class="form-control"
                                            placeholder="sk_live_..."
                                            value="{{ getValuesByKey('stripe_secret_key') ?? '' }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Stripe Webhook Secret</label>
                                        <input type="text" name="stripe_webhook_secret" class="form-control"
                                            placeholder="whsec_..."
                                            value="{{ getValuesByKey('stripe_webhook_secret') ?? '' }}">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Save Stripe Settings</button>
                            </form>
                        </div>

                        <!-- Google Settings -->
                        <div class="tab-pane fade" id="google" role="tabpanel">

                            <form action="{{ route('admin.settings.store') }}" method="post">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="module_name" value="stripe_payment">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Google Client ID</label>
                                        <input type="text" name="google_client_id" class="form-control"
                                            placeholder="Google Client ID"
                                            value="{{ getValuesByKey('google_client_id') ?? '' }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Google Client Secret</label>
                                        <input type="text" name="google_client_secret" class="form-control"
                                            placeholder="Google Client Secret"
                                            value="{{ getValuesByKey('google_client_secret') ?? '' }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Google Refresh Token</label>
                                        <input type="text" name="google_refresh_token" class="form-control"
                                            placeholder="Google Refresh Token"
                                            value="{{ getValuesByKey('google_refresh_token') ?? '' }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Google Calendar ID</label>
                                        <input type="text" name="google_calendar_id" class="form-control"
                                            placeholder="Google Calendar ID"
                                            value="{{ getValuesByKey('google_calendar_id') ?? '' }}">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Save Google Settings</button>
                            </form>

                            <hr class="my-4">

                            <div class="alert alert-warning">
                                <strong>Important:</strong><br>
                                1. First, save the <b>Google Client ID</b> and <b>Client Secret</b> above.<br>
                                2. Make sure this redirect URL is added in the Google Console:
                                <code>{{ url('/google/callback') }}</code> (or your production domain, e.g.
                                <code>https://your-domain.com/google/callback</code>)<br>
                                3. Then click the button below to connect a Google account.
                            </div>

                            @php
                                $googleAuthUrl =
                                    'https://accounts.google.com/o/oauth2/v2/auth' .
                                    '?client_id=' .
                                    urlencode(getValuesByKey('google_client_id')) .
                                    '&redirect_uri=' .
                                    urlencode(url('/google/callback')) .
                                    '&response_type=code' .
                                    '&scope=' .
                                    urlencode('https://www.googleapis.com/auth/calendar') .
                                    '&access_type=offline' .
                                    '&prompt=consent';
                            @endphp

                            <a href="{{ $googleAuthUrl }}"
                                class="btn btn-outline-primary btn-lg d-inline-flex align-items-center gap-2"
                                @if (!getValuesByKey('google_client_id') || !getValuesByKey('google_client_secret')) onclick="event.preventDefault(); alert('Please save Google Client ID and Secret first.');" @endif>
                                <i class="fab fa-google"></i>
                                Start Google Verification
                            </a>
                        </div>

                        <!-- Veriff Settings -->
                        <div class="tab-pane fade" id="veriff" role="tabpanel">

                            <form action="{{ route('admin.settings.store') }}" method="post">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="module_name" value="veriff">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">API Key</label>
                                        <input type="text" name="veriff_api_key" class="form-control"
                                            placeholder="pk_live_..."
                                            value="{{ getValuesByKey('veriff_api_key') ?? '' }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">API Secret Key</label>
                                        <input type="text" name="veriff_secret_key" class="form-control"
                                            placeholder="sk_live_..."
                                            value="{{ getValuesByKey('veriff_secret_key') ?? '' }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">API Base URL</label>
                                        <input type="text" name="veriff_base_url" class="form-control"
                                            placeholder="https://api.veriff.com"
                                            value="{{ getValuesByKey('veriff_base_url') ?? '' }}">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Save Veriff Settings</button>
                            </form>
                        </div>

                        <!-- DocuSign Settings -->
                        <div class="tab-pane fade" id="docusign" role="tabpanel">

                            <form action="{{ route('admin.settings.store') }}" method="post">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="module_name" value="docusign">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">API Key</label>
                                        <input type="text" name="docusign_api_key" class="form-control"
                                            value="{{ getValuesByKey('docusign_api_key') ?? '' }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">API Secret Key</label>
                                        <input type="text" name="docusign_secret_key" class="form-control"
                                            value="{{ getValuesByKey('docusign_secret_key') ?? '' }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">API Base URL</label>
                                        <input type="text" name="docusign_base_url" class="form-control"
                                            value="{{ getValuesByKey('docusign_base_url') ?? '' }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">API Account ID</label>
                                        <input type="text" name="docusign_account_id" class="form-control"
                                            value="{{ getValuesByKey('docusign_account_id') ?? '' }}">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Save DocuSign Settings</button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    @push('scripts')
        <script></script>
    @endpush
@endsection
