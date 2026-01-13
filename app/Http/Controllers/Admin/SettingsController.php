<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
}
