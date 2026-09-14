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
        if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            $settings = \App\Models\Setting::all()->pluck('value', 'key');
            if ($settings->has('MAIL_MAILER')) {
                config([
                    'mail.default' => $settings->get('MAIL_MAILER', 'smtp'),
                    'mail.mailers.smtp.host' => $settings->get('MAIL_HOST', ''),
                    'mail.mailers.smtp.port' => $settings->get('MAIL_PORT', 587),
                    'mail.mailers.smtp.username' => $settings->get('MAIL_USERNAME', ''),
                    'mail.mailers.smtp.password' => $settings->get('MAIL_PASSWORD', ''),
                    'mail.from.address' => $settings->get('MAIL_FROM_ADDRESS', ''),
                    'mail.from.name' => env('MAIL_FROM_NAME', 'YC Content Planning'),
                ]);
            }
        }
        // Dynamically detect current domain when accessed via browser
        if (!app()->runningInConsole() && request()->hasHeader('Host')) {
            try {
                $scheme = request()->isSecure() ? 'https' : 'http';
                $currentHost = $scheme . '://' . request()->getHttpHost();
                \Illuminate\Support\Facades\Cache::forever('app_live_url', $currentHost);
            } catch (\Throwable $e) {}
        } elseif (app()->runningInConsole()) {
            try {
                $cachedUrl = \Illuminate\Support\Facades\Cache::get('app_live_url');
                if ($cachedUrl) {
                    \Illuminate\Support\Facades\URL::forceRootUrl($cachedUrl);
                }
            } catch (\Throwable $e) {}
        }

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
