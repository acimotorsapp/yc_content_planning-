<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $events = \App\Models\CalendarEvent::with('user')->where('team_type', '!=', 'global_team')->orderBy('event_date', 'asc')->get();
    $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
    return view('dashboard', compact('events', 'masterData'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin filtering routes
    Route::get('/admin/events/product', [\App\Http\Controllers\CalendarEventController::class, 'adminProduct'])->name('admin.events.product');
    Route::get('/admin/events/digital', [\App\Http\Controllers\CalendarEventController::class, 'adminDigital'])->name('admin.events.digital');
    Route::get('/admin/events/global', [\App\Http\Controllers\CalendarEventController::class, 'adminGlobal'])->name('admin.events.global');
    
    // Settings Route
    Route::get('/admin/settings', [\App\Http\Controllers\SettingsController::class, 'mailSettings'])->name('admin.settings');
    Route::post('/admin/settings', [\App\Http\Controllers\SettingsController::class, 'updateMailSettings'])->name('admin.settings.update');
    
    // User Management Route
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::put('/admin/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');

    // Master Data Route
    Route::get('/admin/master-data', [\App\Http\Controllers\Admin\MasterDataController::class, 'index'])->name('admin.master_data.index');
    Route::post('/admin/master-data', [\App\Http\Controllers\Admin\MasterDataController::class, 'store'])->name('admin.master_data.store');
    Route::delete('/admin/master-data/{masterData}', [\App\Http\Controllers\Admin\MasterDataController::class, 'destroy'])->name('admin.master_data.destroy');
});

Route::middleware('auth')->group(function () {
    Route::post('/events/product', [\App\Http\Controllers\CalendarEventController::class, 'storeProduct'])->name('events.product.store');
    Route::post('/events/digital', [\App\Http\Controllers\CalendarEventController::class, 'storeDigital'])->name('events.digital.store');
    Route::post('/events/global', [\App\Http\Controllers\CalendarEventController::class, 'storeGlobal'])->name('events.global.store');
    
    Route::get('/events/create', [\App\Http\Controllers\CalendarEventController::class, 'create'])->name('events.create');
    Route::get('/events/{event}', [\App\Http\Controllers\CalendarEventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [\App\Http\Controllers\CalendarEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [\App\Http\Controllers\CalendarEventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [\App\Http\Controllers\CalendarEventController::class, 'destroy'])->name('events.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
