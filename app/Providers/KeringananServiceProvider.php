<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Services\KeringananTagihanService;

class KeringananServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(KeringananTagihanService::class, function ($app) {
            return new KeringananTagihanService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
