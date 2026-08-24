<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function mailSettings()
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        return view('settings');
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
        ]);

        $envPath = base_path('.env');
        
        if (File::exists($envPath)) {
            $envContent = File::get($envPath);

            $keys = [
                'MAIL_MAILER',
                'MAIL_HOST',
                'MAIL_PORT',
                'MAIL_USERNAME',
                'MAIL_PASSWORD',
            ];

            foreach ($keys as $key) {
                $value = $request->input($key);
                // Ensure value is properly quoted if it contains spaces (optional for basic strings but good practice)
                $escapedValue = preg_quote($value, '/');

                // If key exists, replace its value
                if (preg_match("/^{$key}=.*/m", $envContent)) {
                    $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
                } else {
                    // If key does not exist, append it
                    $envContent .= "\n{$key}={$value}\n";
                }
            }

            File::put($envPath, $envContent);
        }

        return back()->with('success', 'Mail settings updated successfully!');
    }
}
