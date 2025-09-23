<?php

namespace Tuner;

use Illuminate\Support\ServiceProvider;

class TunerServiceProvider extends ServiceProvider
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
        $this->publishes([
            __DIR__.'/../config/tuner.php' => config_path('tuner.php'),
        ], 'tuner-config');
    }
}
