<?php

namespace App\Providers;

use App\Interfaces\ServerRepositoryInterface;
use App\Repositories\ServerRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ServerRepositoryInterface::class, ServerRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
