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
