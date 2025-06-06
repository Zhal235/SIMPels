<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Services\KeringananTagihanService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(KeringananTagihanService::class, function ($app) {
            return new KeringananTagihanService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
