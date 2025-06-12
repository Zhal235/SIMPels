<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Services\KeringananTagihanService;
use App\Models\Santri;
use App\Models\Dompet;
use App\Models\Pegawai;
use App\Observers\SantriObserver;
use App\Observers\DompetObserver;
use App\Observers\PegawaiObserver;

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
        // Register model observers
        Santri::observe(SantriObserver::class);
        Dompet::observe(DompetObserver::class);
        Pegawai::observe(PegawaiObserver::class);
    }
}
