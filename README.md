# Laravel-Schema-Helper
Generate portable form schemas from Laravel FormRequests

```bash
composer require kroderdev/schema-helper
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
- `toVueSchema()`: Returns the schema formatted for Vue components.
- `toReactSchema()`: Returns the schema formatted for React components.

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