<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $users = User::orderBy('name', 'asc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['role' => 'You cannot change your own role.']);
        }

        $validated = $request->validate([
            'role' => 'required|in:digital_team,product_team,super_admin',
        ]);

        $user->update(['role' => $validated['role']]);

        return back()->with('success', 'User role updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete yourself.']);
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
