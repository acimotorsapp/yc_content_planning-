<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ManualEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $manualSubject,
        public string $body,
        public array $ccRecipients = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->manualSubject,
            cc: $this->ccRecipients,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.manual_email',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
