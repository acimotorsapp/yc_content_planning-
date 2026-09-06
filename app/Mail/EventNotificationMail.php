<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class EventNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $events;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Collection $events)
    {
        $this->user = $user;
        $this->events = $events;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Add CC recipients while filtering out recipient to prevent duplicates
        $ccRecipients = array_values(array_filter(
            ['option@aci-bd.com', 'sultana.nishi@aci-bd.com'],
            fn($email) => strtolower(trim($email)) !== strtolower(trim($this->user->email ?? ''))
        ));

        return new Envelope(
            subject: 'Your Scheduled Events for Today - YC Content Planning',
            cc: $ccRecipients,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $baseUrl = $this->resolveBaseUrl();

        return new Content(
            view: 'emails.event_notification',
            with: [
                'baseUrl' => $baseUrl,
            ],
        );
    }

    /**
     * Resolve base URL dynamically based on current request or cached live domain.
     */
    protected function resolveBaseUrl(): string
    {
        // 1. If currently inside a web request with Host header
        if (!app()->runningInConsole() && request()->hasHeader('Host')) {
            $scheme = request()->isSecure() ? 'https' : 'http';
            return $scheme . '://' . request()->getHttpHost();
        }

        // 2. Check if cached live domain exists (from user visits)
        try {
            if ($cached = \Illuminate\Support\Facades\Cache::get('app_live_url')) {
                return rtrim($cached, '/');
            }
        } catch (\Throwable $e) {}

        // 3. Check APP_URL from configuration
        $configUrl = config('app.url');
        if (!empty($configUrl) && !str_contains($configUrl, 'localhost')) {
            return rtrim($configUrl, '/');
        }

        return rtrim(url('/'), '/');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
