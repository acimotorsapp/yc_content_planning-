<?php

namespace App\Http\Controllers;

use App\Services\EventNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CronNotificationController extends Controller
{
    /**
     * Trigger event notifications via web/API URL for cron job.
     */
    public function notify(Request $request, EventNotificationService $service): JsonResponse
    {
        // Check for secret token if configured
        $configuredToken = config('event_notifications.cron_token');
        if ($configuredToken && $request->query('token') !== $configuredToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid or missing security token.',
            ], 401);
        }

        $daysAhead = $request->has('days_ahead') ? (int) $request->query('days_ahead') : 5;
        $specificDate = $request->query('date');
        $force = in_array($request->query('force'), ['1', 'true', 'yes'], true);

        $summary = $service->sendNotifications($daysAhead, $specificDate, $force);

        $message = !empty($summary['already_sent'])
            ? $summary['message']
            : "Event reminder notifications processed for target date: {$summary['target_date']} ({$summary['days_ahead']} days ahead).";

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $summary,
        ]);
    }
}
