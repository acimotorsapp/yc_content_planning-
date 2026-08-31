<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CalendarEventController extends Controller
{
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'event_date' => 'required|date|unique:calendar_events,event_date',
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
        ], [
            'event_date.unique' => 'An event is already scheduled on this date (:input). Only 1 event is permitted per date across all teams.',
        ]);

        $validated['team_type'] = 'product_team';
        $validated['boosting_budget'] = !empty($validated['boosting_budget']) ? $validated['boosting_budget'] : '0';
        $request->user()->events()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Product Team Event added successfully!');
    }

    public function storeDigital(Request $request)
    {
        $validated = $request->validate([
            'event_date' => 'required|date|unique:calendar_events,event_date',
            'post_no' => 'nullable|string',
            'aipe_pillar' => 'nullable|string',
            'product_focus' => 'nullable|string',
            'content_objective' => 'nullable|string',
            'format' => 'nullable|string',
            'drive_link' => 'nullable|string',
            'remarks' => 'nullable|string',
            'boosting_budget' => 'nullable|string',
        ], [
            'event_date.unique' => 'An event is already scheduled on this date (:input). Only 1 event is permitted per date across all teams.',
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
        $bookedDates = CalendarEvent::pluck('event_date')->map(fn($d) => $d->format('Y-m-d'))->values()->all();
        return view('dashboard', [
            'events' => $events,
            'filter' => 'My Events',
            'masterData' => $masterData,
            'bookedDates' => $bookedDates,
        ]);
    }

    public function create()
    {
        // For Super Admin to switch between teams, they might need a filter like they have on dashboard.
        $filter = request()->query('filter');
        $events = CalendarEvent::with('user')->orderBy('event_date', 'asc')->get();
        $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
        $bookedDates = CalendarEvent::pluck('event_date')->map(fn($d) => $d->format('Y-m-d'))->values()->all();
        return view('events.create', compact('filter', 'events', 'masterData', 'bookedDates'));
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
        $bookedDates = CalendarEvent::where('id', '!=', $event->id)->pluck('event_date')->map(fn($d) => $d->format('Y-m-d'))->values()->all();
        return view('events.edit', compact('event', 'masterData', 'bookedDates'));
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
                Rule::unique('calendar_events', 'event_date')->ignore($event->id),
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
        ], [
            'event_date.unique' => 'Another event is already scheduled on this date (:input). Only 1 event is permitted per date across all teams.',
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
            'event_date' => 'required|date|unique:calendar_events,event_date',
            'content_title' => 'required|string',
        ], [
            'event_date.unique' => 'An event is already scheduled on this date (:input). Only 1 event is permitted per date across all teams.',
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
        $bookedDates = CalendarEvent::pluck('event_date')->map(fn($d) => $d->format('Y-m-d'))->values()->all();
        return view('dashboard', ['events' => $events, 'filter' => 'Product Team Events', 'masterData' => $masterData, 'bookedDates' => $bookedDates]);
    }

    public function adminDigital()
    {
        if (auth()->user()->role !== 'super_admin') abort(403);
        $events = CalendarEvent::with('user')->where('team_type', 'digital_team')->orderBy('event_date', 'asc')->get();
        $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
        $bookedDates = CalendarEvent::pluck('event_date')->map(fn($d) => $d->format('Y-m-d'))->values()->all();
        return view('dashboard', ['events' => $events, 'filter' => 'Digital Team Events', 'masterData' => $masterData, 'bookedDates' => $bookedDates]);
    }

    public function adminGlobal()
    {
        if (auth()->user()->role !== 'super_admin') abort(403);
        $events = CalendarEvent::with('user')->where('team_type', 'global_team')->orderBy('event_date', 'asc')->get();
        $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
        $bookedDates = CalendarEvent::pluck('event_date')->map(fn($d) => $d->format('Y-m-d'))->values()->all();
        return view('dashboard', ['events' => $events, 'filter' => 'Global Events', 'masterData' => $masterData, 'bookedDates' => $bookedDates]);
    }
}
