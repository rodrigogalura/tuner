<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use RGalura\ApiIgniter\Contracts\ApiIgniterInterface;
use Workbench\App\Repositories\UserRepository;

class ApiIgniterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ApiIgniterInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
