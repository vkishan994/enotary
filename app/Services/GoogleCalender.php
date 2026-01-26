<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class GoogleCalender
{


    public static function getGoogleAccessToken(string $refreshToken): string
    {
        $http = Http::asForm();

        // Disable SSL verification only in local environment
        if (App::environment('local')) {
            $http = $http->withoutVerifying();
        }

        $response = $http->post('https://oauth2.googleapis.com/token', [
            'client_id'     => getValuesByKey('google_client_id'),
            'client_secret' => getValuesByKey('google_client_secret'),
            'refresh_token' => $refreshToken,
            'grant_type'    => 'refresh_token',
        ]);

        if (! $response->successful()) {
            throw new \Exception(
                'Failed to refresh Google access token: ' . $response->body()
            );
        }

        return $response->json()['access_token'];
    }
}
