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
        \Illuminate\Support\Facades\View::composer(['layouts.navigation', 'layouts.app', 'events.partials.create-modal'], function ($view) {
            if (\Illuminate\Support\Facades\Schema::hasTable('master_data')) {
                $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');
                $view->with('masterData', $masterData);
            }
        });
    }
}
