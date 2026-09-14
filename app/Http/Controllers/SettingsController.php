<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function mailSettings()
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }
        
        $settings = Setting::all()->pluck('value', 'key');

        return view('settings', compact('settings'));
    }

    public function updateMailSettings(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $request->validate([
            'MAIL_MAILER' => 'required|string',
            'MAIL_HOST' => 'required|string',
            'MAIL_PORT' => 'required|numeric',
            'MAIL_USERNAME' => 'required|string',
            'MAIL_PASSWORD' => 'required|string',
            'MAIL_FROM_ADDRESS' => 'required|string',
            'MAIL_CC_ADDRESS' => 'nullable|string',
        ]);

        $keys = [
            'MAIL_MAILER',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'MAIL_FROM_ADDRESS',
            'MAIL_CC_ADDRESS',
        ];

        foreach ($keys as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key)]
            );
        }

        // Clear config cache to apply changes immediately
        \Illuminate\Support\Facades\Artisan::call('config:clear');

        return back()->with('success', 'Email configuration updated successfully!');
    }
}
