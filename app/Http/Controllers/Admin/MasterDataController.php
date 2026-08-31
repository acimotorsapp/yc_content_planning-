<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\MasterData;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        // 1. Fetch Master Data grouped by category
        $masterData = MasterData::where('is_active', true)
            ->orderBy('category')
            ->orderBy('value')
            ->get()
            ->groupBy('category');

        // 2. Compute event count for each category value
        $categoryCounts = [];
        $allEvents = CalendarEvent::select('id', 'platform', 'format', 'aipe_pillar', 'product', 'product_focus')->get();
        
        foreach ($masterData as $category => $items) {
            foreach ($items as $item) {
                $val = $item->value;
                $count = 0;
                if ($category === 'platform') {
                    $count = $allEvents->filter(fn($e) => !empty($e->platform) && stripos($e->platform, $val) !== false)->count();
                } elseif ($category === 'format') {
                    $count = $allEvents->filter(fn($e) => !empty($e->format) && stripos($e->format, $val) !== false)->count();
                } elseif ($category === 'aipe_pillar') {
                    $count = $allEvents->filter(fn($e) => !empty($e->aipe_pillar) && stripos($e->aipe_pillar, $val) !== false)->count();
                } elseif ($category === 'product') {
                    $count = $allEvents->filter(fn($e) => 
                        (!empty($e->product) && stripos($e->product, $val) !== false) ||
                        (!empty($e->product_focus) && stripos($e->product_focus, $val) !== false)
                    )->count();
                }
                $categoryCounts[$category][$val] = $count;
            }
        }

        // 3. Build Filtered Events Query
        $eventsQuery = CalendarEvent::with('user')->orderBy('event_date', 'desc');

        // Combined Category Filter (category_type & category_value)
        if ($request->filled('category_type') && $request->filled('category_value')) {
            $catType = $request->category_type;
            $catVal = $request->category_value;

            if ($catType === 'platform') {
                $eventsQuery->where('platform', 'LIKE', "%{$catVal}%");
            } elseif ($catType === 'format') {
                $eventsQuery->where('format', 'LIKE', "%{$catVal}%");
            } elseif ($catType === 'aipe_pillar') {
                $eventsQuery->where('aipe_pillar', 'LIKE', "%{$catVal}%");
            } elseif ($catType === 'product') {
                $eventsQuery->where(function($q) use ($catVal) {
                    $q->where('product', 'LIKE', "%{$catVal}%")
                      ->orWhere('product_focus', 'LIKE', "%{$catVal}%");
                });
            }
        }

        // Individual category filters
        if ($request->filled('platform')) {
            $eventsQuery->where('platform', 'LIKE', "%{$request->platform}%");
        }
        if ($request->filled('format')) {
            $eventsQuery->where('format', 'LIKE', "%{$request->format}%");
        }
        if ($request->filled('aipe_pillar')) {
            $eventsQuery->where('aipe_pillar', 'LIKE', "%{$request->aipe_pillar}%");
        }
        if ($request->filled('product')) {
            $prod = $request->product;
            $eventsQuery->where(function($q) use ($prod) {
                $q->where('product', 'LIKE', "%{$prod}%")
                  ->orWhere('product_focus', 'LIKE', "%{$prod}%");
            });
        }
        if ($request->filled('team_type')) {
            $eventsQuery->where('team_type', $request->team_type);
        }
        if ($request->filled('month')) {
            $month = $request->month;
            if (strlen($month) === 7) {
                $eventsQuery->where('event_date', 'LIKE', "{$month}%");
            } else {
                $eventsQuery->whereMonth('event_date', $month);
            }
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $eventsQuery->where(function($q) use ($search) {
                $q->where('content_title', 'LIKE', "%{$search}%")
                  ->orWhere('content_objective', 'LIKE', "%{$search}%")
                  ->orWhere('product', 'LIKE', "%{$search}%")
                  ->orWhere('product_focus', 'LIKE', "%{$search}%")
                  ->orWhere('platform', 'LIKE', "%{$search}%")
                  ->orWhere('format', 'LIKE', "%{$search}%")
                  ->orWhere('aipe_pillar', 'LIKE', "%{$search}%")
                  ->orWhere('remarks', 'LIKE', "%{$search}%");
            });
        }

        $events = $eventsQuery->paginate(15)->withQueryString()->fragment('events-list');

        $stats = [
            'total_events' => CalendarEvent::count(),
            'filtered_count' => $events->total(),
            'product_events' => CalendarEvent::where('team_type', 'product_team')->count(),
            'digital_events' => CalendarEvent::where('team_type', 'digital_team')->count(),
            'global_events' => CalendarEvent::where('team_type', 'global_team')->count(),
        ];

        return view('admin.master_data.index', compact('masterData', 'categoryCounts', 'events', 'stats'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'category' => 'required|string',
            'value' => 'required|string',
        ]);

        MasterData::firstOrCreate(
            ['category' => $validated['category'], 'value' => $validated['value']],
            ['is_active' => true]
        );

        return back()->with('success', 'Master data added successfully.');
    }

    public function destroy(MasterData $masterData)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $masterData->delete();

        return back()->with('success', 'Master data deleted successfully.');
    }
}
