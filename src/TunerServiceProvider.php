<?php

namespace RodrigoGalura\Tuner;

use Illuminate\Support\ServiceProvider;
use Laradigs\Tweaker\Console\CopyToClipboardTheTruthTable;
use Laradigs\Tweaker\Console\CreateTruthTableCSV;

class TunerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // $this->commands([
        //     CreateTruthTableCSV::class,
        //     CopyToClipboardTheTruthTable::class,
        // ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/tuner.php' => config_path('tuner.php'),
        ]);
    }
}
