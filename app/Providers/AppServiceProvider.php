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
        \Illuminate\Support\Facades\View::composer(
            'components.sidebar',
            \App\Http\View\Composers\SidebarComposer::class
        );

        // Share homepage settings globally
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $settings = \Illuminate\Support\Facades\Cache::remember('homepage_settings', 60 * 24, function () {
                return \App\Models\HomepageSetting::all()->keyBy('key');
            });
            $view->with('settings', $settings);
        });
    }
}
