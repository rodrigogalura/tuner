<?php

namespace Laradigs\Tweaker;

use Illuminate\Support\ServiceProvider;
use Laradigs\Tweaker\Console\CreateTruthTableCSV;
use Laradigs\Tweaker\Console\CopyToClipboardTheTruthTable;

class TweakerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->commands([
            CreateTruthTableCSV::class,
            CopyToClipboardTheTruthTable::class,
        ]);
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
