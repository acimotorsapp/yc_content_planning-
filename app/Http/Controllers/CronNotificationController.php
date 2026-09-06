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
        $configuredToken = env('CRON_TOKEN');
        if ($configuredToken && $request->query('token') !== $configuredToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid or missing security token.',
            ], 401);
        }

        $summary = $service->sendTodayNotifications();

        return response()->json([
            'status' => 'success',
            'message' => 'Event notifications processed successfully.',
            'data' => $summary,
        ]);
    }
}
