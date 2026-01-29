<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Ramsey\Collection\Set;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function store(Request $request)
    {
        $moduleName = $request->module_name;

        // Remove unwanted fields
        $data = $request->except(['_token', 'module_name']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                [
                    'key'         => $key,
                ],
                [
                    'value' => $value,
                    'module_name' => $moduleName,
                ]
            );
        }

        return redirect()
            ->back()
            ->with('success', 'settings updated successfully.');
    }

    public function redirectToGoogle()
    {
        $client = new GoogleClient();

        $client->setClientId(getValuesByKey('google_client_id'));
        $client->setClientSecret(getValuesByKey('google_client_secret'));
        $client->setRedirectUri(route('admin.google.callback'));

        $client->setScopes([
            'https://www.googleapis.com/auth/calendar'
        ]);

        $client->setAccessType('offline'); // REQUIRED for refresh token
        $client->setPrompt('consent');     // FORCE refresh token every time

        return redirect()->away($client->createAuthUrl());
    }

    public function googleCallback(Request $request)
    {
        if (! $request->filled('code')) {
            return redirect()
                ->route('admin.settings')
                ->with('error', 'Google authorization failed or was cancelled.');
        }

        $response = Http::asForm()
            ->when(app()->environment('local'), function ($http) {
                return $http->withoutVerifying(); // local only
            })
            ->post('https://oauth2.googleapis.com/token', [
                'client_id'     => getValuesByKey('google_client_id'),
                'client_secret' => getValuesByKey('google_client_secret'),
                'code'          => $request->code, // 
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => url('/google/callback'),
            ]);

        if (! $response->successful()) {
            Log::error('Google OAuth failed', [
                'response' => $response->body(),
            ]);

            return redirect()
                ->route('admin.settings')
                ->with('error', 'Failed to connect Google Calendar.');
        }

        $tokenData = $response->json();

        // IMPORTANT: Google returns refresh_token only once
        if (! isset($tokenData['refresh_token'])) {
            return redirect()
                ->route('admin.settings')
                ->with(
                    'error',
                    'Refresh token not received. Remove app access from Google Account and try again.'
                );
        }

        // Save refresh token
        Setting::updateOrCreate(
            ['key' => 'google_refresh_token'],
            [
                'value'       => $tokenData['refresh_token'],
                'module_name' => 'google_calendar',
            ]
        );

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Google Calendar connected successfully 🎉');
    }
}
