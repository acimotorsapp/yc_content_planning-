<?php
$setting = \App\Models\Setting::where('key', 'MAIL_CC_ADDRESS')->first();
if ($setting) {
    $newValue = str_replace('Sultana.Nishi@aci-bd.com', 'acijubairislamdaief@gmail.com', $setting->value);
    $setting->update(['value' => $newValue]);
    echo "Updated database CC list.\n";
} else {
    echo "No CC setting found in database.\n";
}
