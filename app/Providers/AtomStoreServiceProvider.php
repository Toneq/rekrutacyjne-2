<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AtomStoreService;

class AtomStoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AtomStoreService::class, function ($app) {
            return new AtomStoreService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
