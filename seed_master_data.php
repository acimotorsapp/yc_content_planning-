<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$data = [
    ['category' => 'platform', 'value' => 'Facebook'],
    ['category' => 'platform', 'value' => 'Instagram'],
    ['category' => 'platform', 'value' => 'Tiktok'],
    ['category' => 'platform', 'value' => 'LinkedIn'],
    ['category' => 'platform', 'value' => 'Youtube'],
    ['category' => 'platform', 'value' => 'YRC Page'],
    ['category' => 'platform', 'value' => 'Yamaha Lovers BD'],
    ['category' => 'format', 'value' => 'Product Review'],
    ['category' => 'format', 'value' => 'OVC'],
    ['category' => 'format', 'value' => 'Special Content'],
    ['category' => 'format', 'value' => 'Get Together'],
    ['category' => 'format', 'value' => 'Reels'],
    ['category' => 'aipe_pillar', 'value' => 'Awareness'],
    ['category' => 'aipe_pillar', 'value' => 'Awareness+Interest'],
    ['category' => 'aipe_pillar', 'value' => 'Interest'],
    ['category' => 'aipe_pillar', 'value' => 'Interest+Experience'],
    ['category' => 'aipe_pillar', 'value' => 'Experience'],
    ['category' => 'product', 'value' => 'FZS V2'],
    ['category' => 'product', 'value' => 'FZS V4'],
    ['category' => 'product', 'value' => 'FZS FI Hybrid'],
    ['category' => 'product', 'value' => 'FZX'],
    ['category' => 'product', 'value' => 'Fazer'],
    ['category' => 'product', 'value' => 'FZ 25'],
    ['category' => 'product', 'value' => 'MT'],
    ['category' => 'product', 'value' => 'R15'],
];
foreach($data as $item) {
    App\Models\MasterData::firstOrCreate($item);
}
echo "Done";
