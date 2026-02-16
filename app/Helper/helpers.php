<?php

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * get settings data
 *
 * @return response()
 */
if (! function_exists('getValuesByKey')) {
    function getValuesByKey($key)
    {
        $settings = Setting::where('key', $key)->value('value');
        if (!empty($settings)) {
            return $settings;
        } else {
            return null;
        }
    }
}

if (!function_exists('paymentStatus')) {
    function paymentStatus($status)
    {
        switch ($status) {
            case 'completed':
                $class = 'success';
                break;

            case 'pending':
                $class = 'warning';
                break;

            case 'failed':
                $class = 'danger';
                break;

            default:
                $class = 'secondary';
                break;
        }

        return '<span class="badge bg-' . $class . '">' . ucfirst($status) . '</span>';
    }
}

if (!function_exists('documentUploadStatus')) {
    function documentUploadStatus($status)
    {
        switch ($status) {
            case 'pending':
                $class = 'secondary';
                $label = 'Pending';
                break;

            case 'submitted':
                $class = 'info';
                $label = 'Submitted';
                break;

            case 'verified':
                $class = 'success';
                $label = 'Verified';
                break;

            case 'reupload':
                $class = 'warning';
                $label = 'Re-upload Required';
                break;

            default:
                $class = 'secondary';
                $label = 'Pending';
                break;
        }

        return '<span class="badge bg-' . $class . '">' . $label . '</span>';
    }
}

if (!function_exists('uploadedEachDocumentStatus')) {
    function uploadedEachDocumentStatus($status)
    {
        switch ($status) {
            case 'pending':
                $class = 'secondary';
                $label = 'Pending';
                break;

            case 'submitted':
                $class = 'info';
                $label = 'Submitted';
                break;

            case 'verified':
                $class = 'success';
                $label = 'Verified';
                break;

            case 'reupload':
                $class = 'warning';
                $label = 'Re-upload Required';
                break;

            default:
                $class = 'dark';
                $label = ucfirst($status);
                break;
        }

        return '<span class="badge bg-' . $class . '">' . $label . '</span>';
    }
}

function exchangeCodeForToken(string $authorizationCode): array
{
    $response = Http::withoutVerifying() // 👈 disables SSL verification
        ->asForm()
        ->post(
            'https://oauth2.googleapis.com/token',
            [
                'client_id'     => getValuesByKey('google_client_id'),
                'client_secret' => getValuesByKey('google_client_secret'),
                'code'          => $authorizationCode,
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => 'http://127.0.0.1:8000/google/callback',
            ]
        );

    if (! $response->successful()) {
        throw new \Exception(
            'Google OAuth token exchange failed: ' . $response->body()
        );
    }

    return $response->json();
}

function getGoogleAccessToken(string $refreshToken): string
{
    $http = Http::asForm();

    // Disable SSL verification only in local environment
    if (App::environment('local')) {
        $http = $http->withoutVerifying();
    }

    $response = $http->post('https://oauth2.googleapis.com/token', [
        'client_id'     => getValuesByKey('google_client_id'),
        'client_secret' => getValuesByKey('google_client_secret'),
        'refresh_token' => decrypt($refreshToken),
        'grant_type'    => 'refresh_token',
    ]);

    if (! $response->successful()) {
        throw new \Exception(
            'Failed to refresh Google access token: ' . $response->body()
        );
    }

    return $response->json()['access_token'];
}

if (!function_exists('meetingStatus')) {
    function meetingStatus($status)
    {
        switch ($status) {
            case 'approved':
                $class = 'success';   // Green – confirmed
                break;

            case 'pending':
                $class = 'warning';   // Yellow – waiting
                break;

            case 'rejected':
                $class = 'danger';    // Red – declined
                break;

            case 'rescheduled':
                $class = 'info';      // Blue – changed, not failed
                break;

            default:
                $class = 'secondary'; // Grey – fallback
                break;
        }

        return '<span class="badge bg-' . $class . '">'
            . ucwords(str_replace('_', ' ', $status)) .
            '</span>';
    }
}

if (!function_exists('generateHash')) {
    function generateHash($filePath)
    {
        if (!Storage::disk('public')->exists($filePath)) {
            return null;
        }

        $fileContent = Storage::get($filePath);

        return hash('sha256', $fileContent);
    }
}
