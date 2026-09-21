<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Sent synchronously (not queued) so notification emails go out immediately
 * from the /cron/events-notify HTTP endpoint without a queue worker.
 */
class EventNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $events;
    public $targetDate;
    public $daysAhead;
    public bool $includeCc;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Collection $events, ?string $targetDate = null, ?int $daysAhead = null, bool $includeCc = true)
    {
        $this->user = $user;
        $this->events = $events;
        $this->targetDate = $targetDate ?? ($events->first()?->event_date?->toDateString() ?? Carbon::today()->toDateString());
        $this->daysAhead = $daysAhead !== null ? $daysAhead : Carbon::today()->diffInDays(Carbon::parse($this->targetDate), false);
        $this->includeCc = $includeCc;
    }

    /**
     * Fallback reminder recipients when Mail CC Address has not been saved yet.
     */
    public static function fallbackReminderRecipients(): array
    {
        return [
            'mirajul@aci-bd.com',
            'richard@aci-bd.com',
            'adhikary@aci-bd.com',
            'efaz@aci-bd.com',
            'Sourav.Bikash@aci-bd.com',
            'Swagata@aci-bd.com',
            'arnob@aci-bd.com',
            'Nabil.Sarker@aci-bd.com',
            'Abu.siddik@aci-bd.com',
            'priasa@aci-bd.com',
            'azmyen@aci-bd.com',
            'Ashif.Ahmed@aci-bd.com',
            'oshin@aci-bd.com',
            'zahidul@aci-bd.com',
            'Daief@aci-bd.com',
        ];
    }

    /**
     * Recipients from Email Configuration → Mail CC Address.
     */
    public static function getDefaultCcRecipients(): array
    {
        $ccSetting = \App\Models\Setting::where('key', 'MAIL_CC_ADDRESS')->value('value');
        $source = !empty($ccSetting) ? $ccSetting : implode(',', self::fallbackReminderRecipients());
        $emails = array_filter(array_map('trim', explode(',', $source)));
        $emails = array_filter($emails, fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL) !== false);

        return array_values(array_unique($emails));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $recipientEmail = strtolower(trim($this->user->email ?? ''));

        $ccRecipients = $this->includeCc ? self::getDefaultCcRecipients() : [];
        $ccRecipients = array_values(array_filter(
            $ccRecipients,
            fn ($email) => strtolower((string) $email) !== $recipientEmail
        ));

        $formattedDate = Carbon::parse($this->targetDate)->format('D, M j, Y');

        if ($this->daysAhead > 0) {
            $subject = "Upcoming Content Reminder: Scheduled for {$formattedDate} (in {$this->daysAhead} days) - YC Content Planning";
        } elseif ($this->daysAhead === 0) {
            $subject = "Content Submission Reminder for Today ({$formattedDate}) - YC Content Planning";
        } else {
            $subject = "Scheduled Content Notice for {$formattedDate} - YC Content Planning";
        }

        return new Envelope(
            subject: $subject,
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
                'targetDate' => $this->targetDate,
                'daysAhead' => $this->daysAhead,
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
