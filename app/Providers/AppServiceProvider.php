<?php

namespace App\Providers;

use App\Services\ConversionServiceInterface;
use App\Services\EloquentConversionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
//        $this->app->bind(
//            ConversionServiceInterface::class,
//            EloquentConversionService::class,
//        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
