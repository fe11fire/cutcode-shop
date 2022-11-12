<?php

namespace Domain\Catalog\Providers;

use Illuminate\Support\ServiceProvider;

// use Illuminate\Support\Facades\Gate;

class CatalogServiceProvider extends ServiceProvider
{

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {

    }

    public function register(): void
    {
        $this->app->register(ActionsServiceProvider::class);
    }
}
