<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer(['layouts.navigation', 'layouts.app', 'events.partials.create-modal', 'events.create', 'events.edit', 'dashboard'], function ($view) {
            if (\Illuminate\Support\Facades\Schema::hasTable('master_data')) {
                $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
                $view->with('masterData', $masterData);
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('calendar_events')) {
                $dateCounts = \App\Models\CalendarEvent::selectRaw('event_date, count(*) as count')
                    ->groupBy('event_date')
                    ->pluck('count', 'event_date')
                    ->mapWithKeys(fn($count, $date) => [\Carbon\Carbon::parse($date)->format('Y-m-d') => (int)$count])
                    ->all();
                $fullyBookedDates = collect($dateCounts)->filter(fn($count) => $count >= 6)->keys()->values()->all();
                $view->with('dateCounts', $dateCounts);
                $view->with('fullyBookedDates', $fullyBookedDates);
            }
        });
    }
}
