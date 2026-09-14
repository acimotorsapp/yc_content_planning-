<?php
use Illuminate\Support\Facades\Mail;

Mail::raw('This is a test email to verify the SMTP configuration from your application database settings.', function ($message) {
    $message->to('acijubairislamdaief@gmail.com')
            ->subject('Test Email from ACI Project');
});
echo "Email sent successfully!\n";
