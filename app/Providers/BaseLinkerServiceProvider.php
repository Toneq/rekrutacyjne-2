<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\BaseLinkerService;

class BaseLinkerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BaseLinkerService::class, function ($app) {
            return new BaseLinkerService();
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
