<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetsSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoogleSheetsSyncController extends Controller
{
    public function sync(Request $request, GoogleSheetsSyncService $service): JsonResponse
    {
        $configuredToken = env('CRON_TOKEN');
        if ($configuredToken && $request->query('token') !== $configuredToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid or missing security token.',
            ], 401);
        }

        $force = in_array($request->query('force'), ['1', 'true', 'yes'], true);
        $summary = $service->sync($force);

        return response()->json([
            'status' => empty($summary['errors']) || ($summary['synced'] ?? false) || ($summary['unchanged'] ?? false)
                ? 'success'
                : 'error',
            'message' => $summary['message'] ?? 'Sync finished.',
            'data' => $summary,
        ]);
    }

    public function status(GoogleSheetsSyncService $service): JsonResponse
    {
        if (auth()->user()?->role !== 'super_admin') {
            abort(403);
        }

        $last = $service->lastSyncedAt();
        $stale = !$last || now()->diffInSeconds($last) > 60;

        if ($stale) {
            try {
                $service->sync();
            } catch (\Throwable $e) {
                // Keep the dashboard usable even if Sheets is briefly unreachable.
            }
        }

        return response()->json([
            'hash' => $service->contentHash(),
            'synced_at' => $service->lastSyncedAt(),
        ]);
    }
}
