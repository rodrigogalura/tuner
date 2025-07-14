<?php

namespace Laradigs\Tweaker;

use Illuminate\Support\ServiceProvider;

class TweakerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/tweaker.php' => config_path('tweaker.php'),
        ]);
    }
}
