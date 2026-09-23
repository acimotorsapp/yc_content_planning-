<?php

namespace App\Providers;

use App\Models\CalendarEvent;
use App\Models\MasterData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
        // Dynamically detect current domain when accessed via browser
        if (! app()->runningInConsole() && request()->hasHeader('Host')) {
            try {
                $scheme = request()->isSecure() ? 'https' : 'http';
                $currentHost = $scheme.'://'.request()->getHttpHost();
                Cache::forever('app_live_url', $currentHost);
            } catch (\Throwable $e) {
            }
        } elseif (app()->runningInConsole()) {
            try {
                $cachedUrl = Cache::get('app_live_url');
                if ($cachedUrl) {
                    URL::forceRootUrl($cachedUrl);
                }
            } catch (\Throwable $e) {
            }
        }

        View::composer(['layouts.navigation', 'layouts.app', 'events.partials.create-modal', 'events.create', 'events.edit', 'dashboard'], function ($view) {
            if (Schema::hasTable('master_data')) {
                $masterData = MasterData::where('is_active', true)->get()->groupBy('category');
                $view->with('masterData', $masterData);
            }
            if (Schema::hasTable('calendar_events')) {
                $dateCounts = CalendarEvent::selectRaw('event_date, count(*) as count')
                    ->groupBy('event_date')
                    ->pluck('count', 'event_date')
                    ->mapWithKeys(fn ($count, $date) => [Carbon::parse($date)->format('Y-m-d') => (int) $count])
                    ->all();
                $fullyBookedDates = collect($dateCounts)->filter(fn ($count) => $count >= 6)->keys()->values()->all();
                $view->with('dateCounts', $dateCounts);
                $view->with('fullyBookedDates', $fullyBookedDates);
            }
        });
    }
}
