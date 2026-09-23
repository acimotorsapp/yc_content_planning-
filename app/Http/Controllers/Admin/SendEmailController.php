<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ManualEmailMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendEmailController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        return view('admin.send_email.index');
    }

    public function send(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $request->merge([
            'to' => trim((string) $request->input('to')),
            'subject' => trim((string) $request->input('subject')),
            'cc' => collect($request->input('cc', []))
                ->map(fn ($email) => trim((string) $email))
                ->values()
                ->all(),
        ]);

        $validated = $request->validate([
            'to' => ['required', 'email'],
            'cc' => ['nullable', 'array'],
            'cc.*' => ['nullable', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $to = $validated['to'];
        $cc = $this->normalizeCcRecipients($validated['cc'] ?? [], $to);

        try {
            Mail::to($to)->send(new ManualEmailMail(
                $validated['subject'],
                $validated['body'],
                $cc
            ));
        } catch (Throwable $exception) {
            Log::error('Manual email could not be sent.', [
                'to' => $to,
                'cc_count' => count($cc),
                'error' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Email could not be sent. Please check the mail configuration or server delivery status.');
        }

        return back()->with('success', 'Email sent successfully.');
    }

    private function normalizeCcRecipients(array $cc, string $to): array
    {
        $to = strtolower(trim($to));
        $seen = [];
        $recipients = [];

        foreach ($cc as $email) {
            $email = trim((string) $email);
            $key = strtolower($email);

            if ($email === '' || $key === $to || isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $recipients[] = $email;
        }

        return $recipients;
    }
}
