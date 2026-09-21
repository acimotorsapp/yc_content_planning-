<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Support\CollectionPaginator;
use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'event_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $count = CalendarEvent::whereDate('event_date', $value)->count();
                    if ($count >= 6) {
                        $fail("A maximum of 6 events can be scheduled on the same date ({$value}). This date is fully booked.");
                    }
                },
            ],
            'content_title' => 'required|string',
            'aipe_pillar' => 'nullable|string',
            'content_objective' => 'nullable|string',
            'shoot_date' => 'nullable|date',
            'color_concern' => 'nullable|string',
            'format' => 'nullable|string',
            'boosting_budget' => 'nullable|string',
            'financial_budget' => 'nullable|string',
            'platform' => 'nullable|string',
            'product' => 'nullable|string',
            'drive_link' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $validated['team_type'] = 'product_team';
        $validated['boosting_budget'] = !empty($validated['boosting_budget']) ? $validated['boosting_budget'] : '0';
        $validated['financial_budget'] = !empty($validated['financial_budget']) ? $validated['financial_budget'] : '0';
        $request->user()->events()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Product Team Event added successfully!');
    }

    public function storeDigital(Request $request)
    {
        $validated = $request->validate([
            'event_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $count = CalendarEvent::whereDate('event_date', $value)->count();
                    if ($count >= 6) {
                        $fail("A maximum of 6 events can be scheduled on the same date ({$value}). This date is fully booked.");
                    }
                },
            ],
            'post_no' => 'nullable|string',
            'aipe_pillar' => 'nullable|string',
            'product_focus' => 'nullable|string',
            'content_title' => 'nullable|string',
            'content_objective' => 'nullable|string',
            'format' => 'nullable|string',
            'drive_link' => 'nullable|string',
            'remarks' => 'nullable|string',
            'boosting_budget' => 'nullable|string',
            'financial_budget' => 'nullable|string',
        ]);

        $validated['team_type'] = 'digital_team';
        $validated['boosting_budget'] = !empty($validated['boosting_budget']) ? $validated['boosting_budget'] : '0';
        $validated['financial_budget'] = !empty($validated['financial_budget']) ? $validated['financial_budget'] : '0';
        if (empty($validated['content_title']) && !empty($validated['product_focus'])) {
            $postNo = $validated['post_no'] ?? '';
            $validated['content_title'] = trim(($postNo !== '' ? "Post #{$postNo}: " : '') . $validated['product_focus']);
        }
        $request->user()->events()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Digital Team Event added successfully!');
    }

    public function storeBrand(Request $request)
    {
        $validated = $request->validate([
            'event_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $count = CalendarEvent::whereDate('event_date', $value)->count();
                    if ($count >= 6) {
                        $fail("A maximum of 6 events can be scheduled on the same date ({$value}). This date is fully booked.");
                    }
                },
            ],
            'content_title' => 'required|string',
            'aipe_pillar' => 'nullable|string',
            'content_objective' => 'nullable|string',
            'format' => 'nullable|string',
            'boosting_budget' => 'nullable|string',
            'financial_budget' => 'nullable|string',
            'platform' => 'nullable|string',
            'product' => 'nullable|string',
            'drive_link' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $validated['team_type'] = 'brand_team';
        $validated['boosting_budget'] = !empty($validated['boosting_budget']) ? $validated['boosting_budget'] : '0';
        $validated['financial_budget'] = !empty($validated['financial_budget']) ? $validated['financial_budget'] : '0';
        $request->user()->events()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Brand Event added successfully!');
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'event_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $count = CalendarEvent::whereDate('event_date', $value)->count();
                    if ($count >= 6) {
                        $fail("A maximum of 6 events can be scheduled on the same date ({$value}). This date is fully booked.");
                    }
                },
            ],
            'content_title' => 'required|string',
            'aipe_pillar' => 'nullable|string',
            'content_objective' => 'nullable|string',
            'format' => 'nullable|string',
            'boosting_budget' => 'nullable|string',
            'financial_budget' => 'nullable|string',
            'platform' => 'nullable|string',
            'product' => 'nullable|string',
            'drive_link' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $validated['team_type'] = 'service_team';
        $validated['boosting_budget'] = !empty($validated['boosting_budget']) ? $validated['boosting_budget'] : '0';
        $validated['financial_budget'] = !empty($validated['financial_budget']) ? $validated['financial_budget'] : '0';
        $request->user()->events()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Service Event added successfully!');
    }

    public function dashboard(Request $request)
    {
        $events = CalendarEvent::with('user')->orderBy('sort_order')->orderBy('event_date')->get();
        $month = $this->resolveMonth($request->query('month'));
        [$year, $monthNum] = array_map('intval', explode('-', $month));

        $monthEvents = $events->filter(function ($event) use ($year, $monthNum) {
            return $event->event_date
                && (int) $event->event_date->year === $year
                && (int) $event->event_date->month === $monthNum;
        })->values();

        $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
        $tableEvents = CollectionPaginator::make($monthEvents, 10)->fragment('schedule');

        return view('dashboard', compact('events', 'masterData', 'tableEvents', 'month', 'monthEvents'));
    }

    public function budgetProvision(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $sort = $request->query('sort', 'high');
        if (!in_array($sort, ['high', 'low'], true)) {
            $sort = 'high';
        }

        $events = CalendarEvent::with('user')
            ->orderBy('event_date', 'asc')
            ->get()
            ->sortBy(function (CalendarEvent $event) use ($sort) {
                $amount = $event->budgetAmount();
                return $sort === 'low' ? $amount : -$amount;
            }, SORT_NUMERIC)
            ->values();

        return view('admin.budget.index', compact('events', 'sort'));
    }

    public function updateTitle(Request $request, CalendarEvent $event)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action. Only Super Admin can edit content titles.');
        }

        $validated = $request->validate([
            'content_title' => 'required|string|max:255',
        ]);

        $event->update(['content_title' => $validated['content_title']]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'content_title' => $event->content_title,
                'message' => 'Content title updated.',
            ]);
        }

        return back()->with('success', 'Content title updated.');
    }

    public function myEvents(Request $request)
    {
        $events = $request->user()->events()->with('user')->orderBy('event_date', 'asc')->get();
        $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
        return view('dashboard', [
            'events' => $events,
            'tableEvents' => CollectionPaginator::make($events, 10)->fragment('schedule'),
            'filter' => 'My Events',
            'masterData' => $masterData,
        ]);
    }

    public function create()
    {
        // For Super Admin to switch between teams, they might need a filter like they have on dashboard.
        $filter = request()->query('filter');
        $events = CalendarEvent::with('user')->orderBy('event_date', 'asc')->get();
        $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
        $tableEvents = CollectionPaginator::make($events, 10)->fragment('schedule');
        return view('events.create', compact('filter', 'events', 'masterData', 'tableEvents'));
    }

    public function show(CalendarEvent $event)
    {
        $event->load('user');
        return view('events.show', compact('event'));
    }

    public function edit(CalendarEvent $event)
    {
        if (auth()->id() !== $event->user_id && auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
        return view('events.edit', compact('event', 'masterData'));
    }

    public function update(Request $request, CalendarEvent $event)
    {
        if (auth()->id() !== $event->user_id && auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'event_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($event) {
                    $count = CalendarEvent::whereDate('event_date', $value)
                        ->where('id', '!=', $event->id)
                        ->count();
                    if ($count >= 6) {
                        $fail("A maximum of 6 events can be scheduled on the same date ({$value}). This date is fully booked.");
                    }
                },
            ],
            'content_title' => 'nullable|string',
            'post_no' => 'nullable|string',
            'aipe_pillar' => 'nullable|string',
            'content_objective' => 'nullable|string',
            'shoot_date' => 'nullable|date',
            'color_concern' => 'nullable|string',
            'format' => 'nullable|string',
            'boosting_budget' => 'nullable|string',
            'financial_budget' => 'nullable|string',
            'platform' => 'nullable|string',
            'product' => 'nullable|string',
            'product_focus' => 'nullable|string',
            'drive_link' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $validated['boosting_budget'] = !empty($validated['boosting_budget']) ? $validated['boosting_budget'] : '0';
        $validated['financial_budget'] = !empty($validated['financial_budget']) ? $validated['financial_budget'] : '0';
        $event->update($validated);

        return redirect()->route('dashboard')->with('success', 'Event updated successfully!');
    }

    public function destroy(Request $request, CalendarEvent $event)
    {
        if (auth()->id() !== $event->user_id && auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $eventId = $event->id;
        $previous = url()->previous();
        $event->delete();

        $fallback = route('dashboard');
        $isEventPage = str_contains($previous, '/events/'.$eventId);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully!',
                'redirect' => $isEventPage ? $fallback : ($previous ?: $fallback),
            ]);
        }

        return redirect()
            ->to($isEventPage ? $fallback : ($previous ?: $fallback))
            ->with('success', 'Event deleted successfully!');
    }

    public function updateStatus(Request $request, CalendarEvent $event)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action. Only Super Admin can update event status.');
        }

        $validated = $request->validate([
            'status' => 'required|in:done,not_done,in_progress',
        ]);

        $event->update([
            'status' => $validated['status'],
        ]);

        $label = match ($event->status) {
            'done' => 'Done',
            'in_progress' => 'In Progress',
            default => 'Not Done',
        };

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $event->status,
                'message' => 'Event marked as '.$label.' successfully!',
            ]);
        }

        return back()->with('success', 'Event marked as '.$label.' successfully!');
    }

    public function reorderBoard(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action. Only Super Admin can reorder the content board.');
        }

        $validated = $request->validate([
            'status' => 'required|in:done,not_done,in_progress',
            'ordered_ids' => 'present|array',
            'ordered_ids.*' => 'integer|exists:calendar_events,id',
        ]);

        foreach ($validated['ordered_ids'] as $index => $id) {
            CalendarEvent::whereKey($id)->update([
                'status' => $validated['status'],
                'sort_order' => $index,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function reschedule(Request $request, CalendarEvent $event)
    {
        if (auth()->id() !== $event->user_id && auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'event_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($event) {
                    $count = CalendarEvent::whereDate('event_date', $value)
                        ->where('id', '!=', $event->id)
                        ->count();
                    if ($count >= 6) {
                        $fail("A maximum of 6 events can be scheduled on the same date ({$value}). This date is fully booked.");
                    }
                },
            ],
            'content_title' => 'nullable|string|max:255',
            'status' => 'nullable|in:done,not_done,in_progress',
        ]);

        $payload = [
            'event_date' => $validated['event_date'],
        ];

        if (! empty($validated['content_title'])) {
            $payload['content_title'] = $validated['content_title'];
        }

        if (! empty($validated['status']) && auth()->user()->role === 'super_admin') {
            $payload['status'] = $validated['status'];
        }

        $event->update($payload);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'event_date' => $event->event_date->format('Y-m-d'),
                'content_title' => $event->displayTitle(),
                'status' => $event->status,
                'message' => 'Content rescheduled.',
            ]);
        }

        return back()->with('success', 'Content rescheduled.');
    }

    public function storeGlobal(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'event_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $count = CalendarEvent::whereDate('event_date', $value)->count();
                    if ($count >= 6) {
                        $fail("A maximum of 6 events can be scheduled on the same date ({$value}). This date is fully booked.");
                    }
                },
            ],
            'content_title' => 'required|string',
        ]);

        CalendarEvent::create([
            'user_id' => auth()->id(),
            'team_type' => 'global_team',
            'event_date' => $validated['event_date'],
            'content_title' => $validated['content_title'],
        ]);

        return back()->with('success', 'Global Event added successfully!');
    }

    public function adminProduct()
    {
        if (auth()->user()->role !== 'super_admin') abort(403);
        $events = CalendarEvent::with('user')->where('team_type', 'product_team')->orderBy('event_date', 'asc')->get();
        $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
        return view('dashboard', [
            'events' => $events,
            'tableEvents' => CollectionPaginator::make($events, 10)->fragment('schedule'),
            'filter' => 'Product Team Events',
            'masterData' => $masterData,
        ]);
    }

    public function adminDigital()
    {
        if (auth()->user()->role !== 'super_admin') abort(403);
        $events = CalendarEvent::with('user')->where('team_type', 'digital_team')->orderBy('event_date', 'asc')->get();
        $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
        return view('dashboard', [
            'events' => $events,
            'tableEvents' => CollectionPaginator::make($events, 10)->fragment('schedule'),
            'filter' => 'Digital Team Events',
            'masterData' => $masterData,
        ]);
    }

    public function adminBrand()
    {
        if (auth()->user()->role !== 'super_admin') abort(403);
        $events = CalendarEvent::with('user')->where('team_type', 'brand_team')->orderBy('event_date', 'asc')->get();
        $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
        return view('dashboard', [
            'events' => $events,
            'tableEvents' => CollectionPaginator::make($events, 10)->fragment('schedule'),
            'filter' => 'Brand Events',
            'masterData' => $masterData,
        ]);
    }

    public function adminService()
    {
        if (auth()->user()->role !== 'super_admin') abort(403);
        $events = CalendarEvent::with('user')->where('team_type', 'service_team')->orderBy('event_date', 'asc')->get();
        $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
        return view('dashboard', [
            'events' => $events,
            'tableEvents' => CollectionPaginator::make($events, 10)->fragment('schedule'),
            'filter' => 'Service Events',
            'masterData' => $masterData,
        ]);
    }

    public function adminGlobal()
    {
        if (auth()->user()->role !== 'super_admin') abort(403);
        $events = CalendarEvent::with('user')->where('team_type', 'global_team')->orderBy('event_date', 'asc')->get();
        $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
        return view('dashboard', [
            'events' => $events,
            'tableEvents' => CollectionPaginator::make($events, 10)->fragment('schedule'),
            'filter' => 'Global Events',
            'masterData' => $masterData,
        ]);
    }

    public function adminDone()
    {
        if (auth()->user()->role !== 'super_admin') abort(403);
        $events = CalendarEvent::with('user')->where('status', 'done')->orderBy('event_date', 'asc')->get();
        $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
        return view('dashboard', [
            'events' => $events,
            'tableEvents' => CollectionPaginator::make($events, 10)->fragment('schedule'),
            'filter' => 'Done Events',
            'masterData' => $masterData,
        ]);
    }

    public function adminNotDone()
    {
        if (auth()->user()->role !== 'super_admin') abort(403);
        $events = CalendarEvent::with('user')->where(function($query) {
            $query->where('status', '!=', 'done')->orWhereNull('status');
        })->orderBy('event_date', 'asc')->get();
        $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
        return view('dashboard', [
            'events' => $events,
            'tableEvents' => CollectionPaginator::make($events, 10)->fragment('schedule'),
            'filter' => 'Not Done Events',
            'masterData' => $masterData,
        ]);
    }

    private function resolveMonth(?string $month): string
    {
        if (is_string($month) && preg_match('/^\d{4}-\d{2}$/', $month)) {
            return $month;
        }

        return now()->format('Y-m');
    }
}
