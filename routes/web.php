<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\CalendarEventController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin filtering routes
    Route::get('/admin/events/product', [\App\Http\Controllers\CalendarEventController::class, 'adminProduct'])->name('admin.events.product');
    Route::get('/admin/events/digital', [\App\Http\Controllers\CalendarEventController::class, 'adminDigital'])->name('admin.events.digital');
    Route::get('/admin/events/global', [\App\Http\Controllers\CalendarEventController::class, 'adminGlobal'])->name('admin.events.global');
    Route::get('/admin/events/done', [\App\Http\Controllers\CalendarEventController::class, 'adminDone'])->name('admin.events.done');
    Route::get('/admin/events/not-done', [\App\Http\Controllers\CalendarEventController::class, 'adminNotDone'])->name('admin.events.not_done');
    Route::get('/admin/budget', [\App\Http\Controllers\CalendarEventController::class, 'budgetProvision'])->name('admin.budget.index');
    Route::get('/admin/sheets/status', [\App\Http\Controllers\GoogleSheetsSyncController::class, 'status'])->name('admin.sheets.status');
    
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

    // Bulk Upload Route
    Route::get('/admin/bulk-upload', [\App\Http\Controllers\Admin\BulkUploadController::class, 'index'])->name('admin.bulk_upload.index');
    Route::post('/admin/bulk-upload/events', [\App\Http\Controllers\Admin\BulkUploadController::class, 'uploadEvents'])->name('admin.bulk_upload.events');
    Route::post('/admin/bulk-upload/master-data', [\App\Http\Controllers\Admin\BulkUploadController::class, 'uploadMasterData'])->name('admin.bulk_upload.master_data');
    Route::get('/admin/bulk-upload/sample/{type}', [\App\Http\Controllers\Admin\BulkUploadController::class, 'downloadSample'])->name('admin.bulk_upload.sample');
});

Route::middleware('auth')->group(function () {
    Route::post('/events/product', [\App\Http\Controllers\CalendarEventController::class, 'storeProduct'])->name('events.product.store');
    Route::post('/events/digital', [\App\Http\Controllers\CalendarEventController::class, 'storeDigital'])->name('events.digital.store');
    Route::post('/events/global', [\App\Http\Controllers\CalendarEventController::class, 'storeGlobal'])->name('events.global.store');
    
    Route::get('/my-events', [\App\Http\Controllers\CalendarEventController::class, 'myEvents'])->name('events.my');
    Route::get('/events/create', [\App\Http\Controllers\CalendarEventController::class, 'create'])->name('events.create');
    Route::get('/events/{event}', [\App\Http\Controllers\CalendarEventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [\App\Http\Controllers\CalendarEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [\App\Http\Controllers\CalendarEventController::class, 'update'])->name('events.update');
    Route::patch('/events/{event}/title', [\App\Http\Controllers\CalendarEventController::class, 'updateTitle'])->name('events.update_title');
    Route::delete('/events/{event}', [\App\Http\Controllers\CalendarEventController::class, 'destroy'])->name('events.destroy');
    Route::patch('/events/{event}/status', [\App\Http\Controllers\CalendarEventController::class, 'updateStatus'])->name('events.update_status');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Public Cron Notification Endpoint (Allows triggering daily mail via Web URL / cPanel curl / external cron)
Route::match(['get', 'post'], '/cron/events-notify', [\App\Http\Controllers\CronNotificationController::class, 'notify'])->name('cron.events.notify');
Route::match(['get', 'post'], '/api/cron/events-notify', [\App\Http\Controllers\CronNotificationController::class, 'notify'])->name('api.cron.events.notify');
Route::match(['get', 'post'], '/cron/sheets-sync', [\App\Http\Controllers\GoogleSheetsSyncController::class, 'sync'])->name('cron.sheets.sync');
Route::match(['get', 'post'], '/api/cron/sheets-sync', [\App\Http\Controllers\GoogleSheetsSyncController::class, 'sync'])->name('api.cron.sheets.sync');

