<?php

return [
    /**
     * --------------------------------------------------------------------------
     * Schema Helper Configuration
     * --------------------------------------------------------------------------
     *
     * Define how the Schema Helper package should register its routes and
     * map schema names to FormRequest classes.
     */



    /*
     * Route Registration Mode:
     *  - 'inline': register schema routes inline under the API middleware.
     *  - 'file': load schema routes from a separate routes file (see 'routes_file').
     */
    'route_mode' => env('SCHEMA_HELPER_ROUTE_MODE', 'inline'), // 'inline' or 'file'



    /**
     * Route Prefix:
     * Used only in 'inline' mode to prefix all schema endpoints.
     */
    'route_prefix' => env('SCHEMA_HELPER_PREFIX', 'api/schemas'),



    /**
     * Export Method:
     * Determines how the schema is exported in the route response.
     * Available options:
     *  - 'toJsonResponse': Export as a JSON response.
     *  - 'toVueSchema':    Export as a Vue.js compatible schema.
     *  - 'toReactSchema':  Export as a React compatible schema.
     */
    'export_method' => env('SCHEMA_HELPER_EXPORT_METHOD', 'toJsonResponse'),



    /**
     * Schemas:
     * Associate a key (used in URI) with a FormRequest class that
     * defines the validation rules to convert into a form schema.
     *
     * Example:
     * 'user'    => \App\Http\Requests\UserFormRequest::class,
     * 'post'    => \App\Http\Requests\PostFormRequest::class,
     */
    'schemas' => [
        // 'user'    => \App\Http\Requests\UserFormRequest::class,
        // 'post'    => \App\Http\Requests\PostFormRequest::class,
    ],



    /**
     * Routes File Path:
     * Used only in 'file' mode to load routes from an external file.
     * Defaults to 'routes/schema-helper.php'.
     */
    'routes_file' => base_path('routes/schema-helper.php'),
];