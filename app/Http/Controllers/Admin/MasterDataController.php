<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterData;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $masterData = MasterData::orderBy('category')->orderBy('value')->get()->groupBy('category');
        return view('admin.master_data.index', compact('masterData'));
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

        MasterData::create($validated);

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
