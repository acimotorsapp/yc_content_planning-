<?php

use App\Http\Controllers\Admin\BulkUploadController;
use App\Http\Controllers\Admin\EventNotificationDeliveryController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\SendEmailController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\CronNotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Models\CalendarEvent;
use App\Models\MasterData;
use App\Support\CollectionPaginator;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $events = CalendarEvent::with('user')->orderBy('event_date', 'asc')->get();
    $masterData = MasterData::where('is_active', true)->get()->groupBy('category');
    // $events feeds the calendar (needs every event); the table pages through the same collection.
    $tableEvents = CollectionPaginator::make($events, 10)->fragment('schedule');

    return view('dashboard', compact('events', 'masterData', 'tableEvents'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin filtering routes
    Route::get('/admin/events/product', [CalendarEventController::class, 'adminProduct'])->name('admin.events.product');
    Route::get('/admin/events/digital', [CalendarEventController::class, 'adminDigital'])->name('admin.events.digital');
    Route::get('/admin/events/global', [CalendarEventController::class, 'adminGlobal'])->name('admin.events.global');
    Route::get('/admin/events/done', [CalendarEventController::class, 'adminDone'])->name('admin.events.done');
    Route::get('/admin/events/not-done', [CalendarEventController::class, 'adminNotDone'])->name('admin.events.not_done');

    // Settings Route
    Route::get('/admin/settings', [SettingsController::class, 'mailSettings'])->name('admin.settings');
    Route::post('/admin/settings', [SettingsController::class, 'updateMailSettings'])->name('admin.settings.update');

    // Send Email Route
    Route::get('/admin/send-email', [SendEmailController::class, 'index'])->name('admin.send-email.index');
    Route::post('/admin/send-email', [SendEmailController::class, 'send'])->name('admin.send-email.send');

    // Automatic event notification delivery logs
    Route::get('/admin/notification-logs', [EventNotificationDeliveryController::class, 'index'])->name('admin.notification-logs.index');
    Route::post('/admin/notification-logs/{delivery}/retry', [EventNotificationDeliveryController::class, 'retry'])->name('admin.notification-logs.retry');

    // User Management Route
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    // Master Data Route
    Route::get('/admin/master-data', [MasterDataController::class, 'index'])->name('admin.master_data.index');
    Route::post('/admin/master-data', [MasterDataController::class, 'store'])->name('admin.master_data.store');
    Route::delete('/admin/master-data/{masterData}', [MasterDataController::class, 'destroy'])->name('admin.master_data.destroy');

    // Bulk Upload Route
    Route::get('/admin/bulk-upload', [BulkUploadController::class, 'index'])->name('admin.bulk_upload.index');
    Route::post('/admin/bulk-upload/events', [BulkUploadController::class, 'uploadEvents'])->name('admin.bulk_upload.events');
    Route::post('/admin/bulk-upload/master-data', [BulkUploadController::class, 'uploadMasterData'])->name('admin.bulk_upload.master_data');
    Route::get('/admin/bulk-upload/sample/{type}', [BulkUploadController::class, 'downloadSample'])->name('admin.bulk_upload.sample');
});

Route::middleware('auth')->group(function () {
    Route::post('/events/product', [CalendarEventController::class, 'storeProduct'])->name('events.product.store');
    Route::post('/events/digital', [CalendarEventController::class, 'storeDigital'])->name('events.digital.store');
    Route::post('/events/global', [CalendarEventController::class, 'storeGlobal'])->name('events.global.store');

    Route::get('/my-events', [CalendarEventController::class, 'myEvents'])->name('events.my');
    Route::get('/events/create', [CalendarEventController::class, 'create'])->name('events.create');
    Route::get('/events/{event}', [CalendarEventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [CalendarEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [CalendarEventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [CalendarEventController::class, 'destroy'])->name('events.destroy');
    Route::patch('/events/{event}/status', [CalendarEventController::class, 'updateStatus'])->name('events.update_status');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Public Cron Notification Endpoint (Allows triggering daily mail via Web URL / cPanel curl / external cron)
Route::match(['get', 'post'], '/cron/events-notify', [CronNotificationController::class, 'notify'])->name('cron.events.notify');
Route::match(['get', 'post'], '/api/cron/events-notify', [CronNotificationController::class, 'notify'])->name('api.cron.events.notify');
