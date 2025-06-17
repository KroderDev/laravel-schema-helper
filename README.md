# Laravel-Schema-Helper
Generate portable form schemas from Laravel FormRequests

## Installation

```bash
composer require kroderdev/laravel-schema-helper
```

### Optional: Publish the Configuration File

To customize the default behavior of the Schema Helper package, you may publish the configuration file:

```bash
php artisan vendor:publish --tag=config --provider="Kroderdev\SchemaHelper\SchemaServiceProvider"
```


## Basic Usage

```php
use Kroderdev\SchemaHelper\SchemaHelper;

$schema = SchemaHelper::generateFromRequest(new ModelStoreRequest())
    ->withOptions([
        'my-select' => ['1' => 'Option 1', '2' => 'Option 2' ...],
    ])
    ->toArray();
```

## Available Exports
- `toArray()`: Returns the schema as a PHP array.
- `toJson()`: Returns the schema as a JSON string.
- `toJsonResponse()`: Returns the schema as a Laravel JSON response.
- `toVueSchema()`: Returns the schema formatted for Vue components. (Alpha, test required)
- `toReactSchema()`: Returns the schema formatted for React components. (Alpha, test required)

## Output Example

```json
[
    {
        "name": "item_name",
        "type": "string",
        "required": true,
        "label": "Item Name",
    },
    {
        "name": "item_code",
        "type": "string",
        "required": true,
        "label": "Item Code",
    },
    {
        "name": "category",
        "type": "select",
        "required": true,
        "label": "Category",
        "options": {
        "1": "Option A",
        "2": "Option B",
        "3": "Option C",
        "4": "Option D",
        "5": "Option E",
        "6": "Option F"
        }
    },
    {
        "name": "is_component",
        "type": "checkbox",
        "required": false,
        "label": "Is Component",
    },
]
```

## Route Registration Modes

By default, all schema endpoints are registered **inline** under the `api` middleware, using the prefix defined in `config/schema-helper.php`.

### Inline Mode (default)

```php
Route::middleware('api')->prefix('api/schemas')->group(function() {
    Route::get('/', …)->name('schemas.index');
    Route::get('user', …)->name('schemas.user');
    // etc.
});
```

* **Toggle it off** by setting in your `.env`:

```dotenv
SCHEMA_HELPER_ROUTE_MODE=file
```

### File Mode

If you prefer to keep your routes in a separate file, switch to **file** mode:

1. Publish the config and set the mode:

    ```bash
    php artisan vendor:publish --tag=config
    ```

    ```dotenv
    SCHEMA_HELPER_ROUTE_MODE=file
    SCHEMA_HELPER_ROUTES_FILE=routes/schema-helper.php
    ```

2. Create `routes/schema-helper.php` in your app:

    ```php
    <?php

    use Illuminate\Support\Facades\Route;
    use Kroderdev\SchemaHelper\SchemaHelper;

    $schemas = config('schema-helper.schemas', []);

    Route::get('/schemas', function() use ($schemas) {
        $list = [];
        foreach ($schemas as $key => $req) {
            $list[$key] = route("schemas.$key", [], false);
        }
        return response()->json(['available_schemas' => $list]);
    })->name('schemas.index');

    foreach ($schemas as $key => $req) {
        Route::get("/schemas/{$key}", function() use ($req) {
            return response()->json(
                SchemaHelper::fromRequest(new $req())->toArray()
            );
        })->name("schemas.{$key}");
    }
    ```

Now you have full control: **inline** mode for zero-touch endpoint registration, or **file** mode if you’d rather manage all routes in your own `routes/` folder.
