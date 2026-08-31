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
                    $count = CalendarEvent::where('event_date', $value)->count();
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
            'platform' => 'nullable|string',
            'product' => 'nullable|string',
            'drive_link' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $validated['team_type'] = 'product_team';
        $validated['boosting_budget'] = !empty($validated['boosting_budget']) ? $validated['boosting_budget'] : '0';
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
                    $count = CalendarEvent::where('event_date', $value)->count();
                    if ($count >= 6) {
                        $fail("A maximum of 6 events can be scheduled on the same date ({$value}). This date is fully booked.");
                    }
                },
            ],
            'post_no' => 'nullable|string',
            'aipe_pillar' => 'nullable|string',
            'product_focus' => 'nullable|string',
            'content_objective' => 'nullable|string',
            'format' => 'nullable|string',
            'drive_link' => 'nullable|string',
            'remarks' => 'nullable|string',
            'boosting_budget' => 'nullable|string',
        ]);

        $validated['team_type'] = 'digital_team';
        $validated['boosting_budget'] = !empty($validated['boosting_budget']) ? $validated['boosting_budget'] : '0';
        $request->user()->events()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Digital Team Event added successfully!');
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
                    $count = CalendarEvent::where('event_date', $value)
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
            'platform' => 'nullable|string',
            'product' => 'nullable|string',
            'product_focus' => 'nullable|string',
            'drive_link' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $validated['boosting_budget'] = !empty($validated['boosting_budget']) ? $validated['boosting_budget'] : '0';
        $event->update($validated);

        return redirect()->route('dashboard')->with('success', 'Event updated successfully!');
    }

    public function destroy(CalendarEvent $event)
    {
        if (auth()->id() !== $event->user_id && auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $event->delete();
        return back()->with('success', 'Event deleted successfully!');
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
                    $count = CalendarEvent::where('event_date', $value)->count();
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
}
