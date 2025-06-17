<?php

namespace Kroderdev\SchemaHelper;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Kroderdev\SchemaHelper\SchemaHelper;

class SchemaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge package configuration with application's published copy
        $this->mergeConfigFrom(__DIR__.'/../config/schema-helper.php', 'schema-helper');
        
        $this->app->singleton(SchemaHelper::class, fn() => new SchemaHelper());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/schema-helper.php' => config_path('schema-helper.php'),
        ], 'config');

        // How to load the routes
        $mode = config('schema-helper.route_mode', 'inline');

        if ($mode === 'inline') {
            $this->registerInlineRoutes();
        } elseif ($mode === 'file') {
            $this->registerRoutesFromFile();
        }
        
    }

    /**
     * Registers inline routes with the appropriate prefix and middleware.
     */
    protected function registerInlineRoutes(): void
    {
        $prefix       = config('schema-helper.route_prefix', 'api/schemas');
        $exportMethod = config('schema-helper.export_method', 'toJsonResponse');
        $schemas      = config('schema-helper.schemas', []);

        Route::prefix($prefix)
            ->middleware('api')
            ->group(function () use ($schemas, $exportMethod) {
                // Index endpoint: GET /api/schemas
                Route::get('/', function () use ($schemas) {
                    return response()->json([
                        'schemas' => array_keys(config('schema-helper.schemas', [])),
                    ]);
                });

                // Individual schema endpoints: GET /api/schemas/{key}
                foreach ($schemas as $key => $requestClass) {
                    Route::get($key, function () use ($requestClass, $exportMethod) {
                        $instance = new $requestClass;
                        $builder  = SchemaHelper::generateFromRequest($instance);
                        return $builder->{$exportMethod}();
                    })->name("schemas.{$key}");
                }
            });
    }

    /**
     * Load routes from an external file.
     */
    protected function registerRoutesFromFile(): void
    {
        $routesPath = config('schema-helper.routes_file', base_path('routes/schema-helper.php'));

        if (file_exists($routesPath)) {
            $this->loadRoutesFrom($routesPath);
        }
    }
}
