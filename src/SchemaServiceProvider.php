<?php

namespace Kroderdev\SchemaHelper;

use Illuminate\Support\ServiceProvider;
use Kroderdev\SchemaHelper\SchemaHelper;

class SchemaServiceProvider extends ServiceProvider
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
        $this->app->singleton(SchemaHelper::class, fn() => new SchemaHelper());
    }
}
