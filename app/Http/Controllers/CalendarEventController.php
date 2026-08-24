<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'event_date' => 'required|date',
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
        $request->user()->events()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Product Team Event added successfully!');
    }

    public function storeDigital(Request $request)
    {
        $validated = $request->validate([
            'event_date' => 'required|date',
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
        $request->user()->events()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Digital Team Event added successfully!');
    }

    public function update(Request $request, CalendarEvent $event)
    {
        if (auth()->id() !== $event->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'event_date' => 'required|date',
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

        $event->update($validated);

        return redirect()->route('dashboard')->with('success', 'Event updated successfully!');
    }

    public function destroy(CalendarEvent $event)
    {
        if (auth()->id() !== $event->user_id) {
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
            'event_date' => 'required|date',
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
        return view('dashboard', ['events' => $events, 'filter' => 'Product Team Events']);
    }

    public function adminDigital()
    {
        if (auth()->user()->role !== 'super_admin') abort(403);
        $events = CalendarEvent::with('user')->where('team_type', 'digital_team')->orderBy('event_date', 'asc')->get();
        return view('dashboard', ['events' => $events, 'filter' => 'Digital Team Events']);
    }

    public function adminGlobal()
    {
        if (auth()->user()->role !== 'super_admin') abort(403);
        $events = CalendarEvent::with('user')->where('team_type', 'global_team')->orderBy('event_date', 'asc')->get();
        return view('dashboard', ['events' => $events, 'filter' => 'Global Events']);
    }
}
