<?php

return [
    'days_ahead' => (int) env('EVENT_NOTIFICATION_DAYS_AHEAD', 5),
    'prepare_time' => env('EVENT_NOTIFICATION_PREPARE_TIME', '08:00'),
    'timezone' => env('EVENT_NOTIFICATION_TIMEZONE', 'Asia/Dhaka'),
    'stale_processing_minutes' => (int) env('EVENT_NOTIFICATION_STALE_PROCESSING_MINUTES', 15),
    'cron_token' => env('CRON_TOKEN'),
];
