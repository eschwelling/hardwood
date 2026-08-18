<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        // Laravel defaults to the Tailwind paginator views, but this app
        // has no compiled stylesheet for those utility classes to resolve
        // against. Use the app's own markup instead.
        Paginator::defaultView('pagination.rafters');
        Paginator::defaultSimpleView('pagination.rafters');
    }
}
