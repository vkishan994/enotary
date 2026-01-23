<?php

use Carbon\Carbon;
use App\Models\Setting;

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
