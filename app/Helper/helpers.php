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
        if (empty($status)) {
            return '<span class="badge bg-secondary">Not Scheduled</span>';
        }

        switch ($status) {
            case 'approved':
                $class = 'success';   // Green – confirmed
                break;

            case 'verified':
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

if (!function_exists('orderStepsCompletedCount')) {
    function orderStepsCompletedCount($order)
    {
        $completed = 0;

        // Step 1: Veriff Approved
        if ($order->veriffData && $order->veriffData->status === 'approved') {
            $completed++;
        }

        // Step 2: Documents Verified
        if ($order->all_docs_verified) {
            $completed++;
        }

        // Step 3: Meeting Verified
        if ($order->scheduleMeeting && $order->scheduleMeeting->status === 'verified') {
            $completed++;
        }

        // Step 4: E-notary Generated
        if ($order->generateEnotaryDoc && $order->generateEnotaryDoc->status === 'generated') {
            $completed++;
        }

        return $completed; // returns 0 - 4
    }
}

if (!function_exists('orderStepStatus')) {
    function orderStepStatus($order)
    {
        $steps = [
            'veriff' => $order->veriffData
                && $order->veriffData->status === 'approved',

            'documents' => $order->all_docs_verified,

            'meeting' => $order->scheduleMeeting
                && $order->scheduleMeeting->status === 'verified',

            'enotary' => $order->generateEnotaryDoc
                && $order->generateEnotaryDoc->status === 'generated',
        ];

        $result = [];
        $previousCompleted = true;

        foreach ($steps as $key => $isCompleted) {

            if ($isCompleted) {
                $result[$key] = 'complete';
            } elseif ($previousCompleted) {
                $result[$key] = 'pending';
            } else {
                $result[$key] = 'locked';
            }

            $previousCompleted = $isCompleted;
        }

        return $result;
    }
}

// if (!function_exists('veriffStatus')) {
//     function veriffStatus($status)
//     {

//         if (empty($status)) {
//             return '<span class="badge bg-secondary">Not Started</span>';
//         }
//         switch ($status) {
//             case 'created':
//                 $class = 'secondary';
//                 break;

//             case 'started':
//                 $class = 'info';
//                 break;

//             case 'submitted':
//                 $class = 'primary';
//                 break;

//             case 'approved':
//                 $class = 'success';
//                 break;

//             case 'declined':
//                 $class = 'danger';
//                 break;

//             case 'resubmission_requested':
//                 $class = 'warning';
//                 break;

//             case 'expired':
//                 $class = 'dark';
//                 break;

//             case 'abandoned':
//                 $class = 'dark';
//                 break;

//             case 'review':
//                 $class = 'warning';
//                 break;

//             case 'user_cancelled':
//                 $class = 'dark';
//                 break;

//             default:
//                 $class = 'secondary';
//                 break;
//         }

//         // Make status label more readable
//         $label = ucwords(str_replace('_', ' ', $status));

//         return '<span class="badge bg-' . $class . '">' . $label . '</span>';
//     }
// }

if (!function_exists('veriffStatus')) {
    function veriffStatus($status)
    {
        $status = $status ?? 'not_started';

        switch ($status) {

            case 'approved':
                return '<span class="badge bg-success">
                            <i class="fa fa-check-circle me-1"></i> Verified
                        </span>';

            case 'started':
            case 'submitted':
                return '<span class="badge bg-warning text-dark">
                            <i class="fa fa-clock me-1"></i> Verification in progress
                        </span>';

            case 'resubmission_requested':
                return '<span class="badge bg-info text-dark">
                            <i class="fa fa-redo me-1"></i> Action required
                        </span>';

            case 'declined':
                return '<span class="badge bg-danger">
                            <i class="fa fa-times-circle me-1"></i> Verification failed
                        </span>';

            case 'expired':
            case 'abandoned':
                return '<span class="badge bg-secondary">
                            <i class="fa fa-ban me-1"></i> Verification expired
                        </span>';

            case 'created':
                return '<span class="badge bg-light text-dark">
                            <i class="fa fa-info-circle me-1"></i> Created
                        </span>';

            default:
                return '<span class="badge bg-secondary">
                            <i class="fa fa-question-circle me-1"></i> Not started
                        </span>';
        }
    }
}
