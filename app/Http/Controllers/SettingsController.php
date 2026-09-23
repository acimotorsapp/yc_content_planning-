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
            'MAIL_CC_ADDRESS' => 'nullable|string',
        ]);

        Setting::updateOrCreate(
            ['key' => 'MAIL_CC_ADDRESS'],
            ['value' => $request->input('MAIL_CC_ADDRESS')]
        );

        return back()->with('success', 'Mail CC settings updated successfully.');
    }
}
