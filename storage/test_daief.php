<?php
// Quick test: send one plain mail to Daief@aci-bd.com via active mailer
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    \Illuminate\Support\Facades\Mail::raw(
        "Hello Daief,\n\nThis is a test email from the YC Content Planning system.\n\nIf you received this, email delivery is working fine.\n\n- YC Content Planning (" . now()->format('d M Y, h:i A') . ")",
        function ($m) {
            $m->to('Daief@aci-bd.com')->subject('Test Email - YC Content Planning');
        }
    );
    echo "SENT OK\n";
} catch (\Throwable $e) {
    echo "FAILED: " . substr($e->getMessage(), 0, 120) . "\n";
}
