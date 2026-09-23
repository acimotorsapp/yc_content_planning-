<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventNotificationDelivery;
use App\Models\User;
use Illuminate\Http\Request;

class EventNotificationDeliveryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        $query = EventNotificationDelivery::query()->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('target_date')) {
            $query->whereDate('target_date', $request->date('target_date')->toDateString());
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $matchingUserIds = User::query()
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->pluck('id');

            if ($matchingUserIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where(function ($deliveryQuery) use ($matchingUserIds) {
                    foreach ($matchingUserIds as $userId) {
                        $deliveryQuery->orWhereJsonContains('user_ids', (int) $userId);
                    }
                });
            }
        }

        $deliveries = $query->paginate(50)->withQueryString();
        $usersById = User::query()
            ->whereKey(
                $deliveries->getCollection()
                    ->flatMap(fn (EventNotificationDelivery $delivery) => $delivery->user_ids ?? [])
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values()
                    ->all()
            )
            ->get()
            ->keyBy('id');

        return view('admin.notification_logs.index', compact('deliveries', 'usersById'));
    }

    public function retry(EventNotificationDelivery $delivery)
    {
        $this->authorizeSuperAdmin();

        if ($delivery->status !== EventNotificationDelivery::STATUS_FAILED) {
            return back()->with('error', 'Only failed notifications can be retried.');
        }

        $delivery->update([
            'status' => EventNotificationDelivery::STATUS_PENDING,
            'last_error' => null,
            'processing_started_at' => null,
            'sent_at' => null,
        ]);

        return back()->with('success', 'Notification retry has been queued.');
    }

    private function authorizeSuperAdmin(): void
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
    }
}
