<?php

namespace Kroderdev\SchemaHelper;

use Illuminate\Support\Str;

class SchemaBuilder
{
    protected array $schema;

    public function __construct(array $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Injects options into fields of type `select` or any field
     * whose name is in the mapping.
     *
     * @param  array  $options  ['fieldName' => [options array], ...]
     * @return $this
     */
    public function withOptions(array $options = []): self
    {
        foreach ($this->schema as &$field) {
            // Campos anidados
            if (isset($field['fields']) && is_array($field['fields'])) {
                foreach ($field['fields'] as &$sub) {
                    if (isset($options[$sub['name']])) {
                        $sub['options'] = $options[$sub['name']];
                    }
                }
            }

            // Campo plano
            if (isset($options[$field['name']])) {
                $field['options'] = $options[$field['name']];
            }
        }

        return $this;
    }

    /**
     * Returns the schema as an array.
     */
    public function toArray(): array
    {
        return $this->schema;
    }

    /**
     * Returns the schema as a JSON string.
     *
     * @return string The schema encoded as a pretty-printed JSON string.
     */
    public function toJson(): string
    {
        return json_encode($this->schema, JSON_PRETTY_PRINT);
    }

    /**
     * Returns an HTTP JSON response containing the schema.
     *
     * @param int $status The HTTP status code for the response (default is 200).
     * @return \Illuminate\Http\JsonResponse The JSON response with the schema.
     */
    public function toJsonResponse(int $status = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->schema, $status);
    }

    /**
     * Generates a schema ready for dynamic use in Vue.js.
     *
     * @return array The generated schema formatted for Vue.js consumption.
     */
    public function toVueSchema(): array
    {
        $vue = [];
        foreach ($this->schema as $f) {
            $item = [
                'model'    => $f['name'],
                'label'    => $f['label'],
                'type'     => $this->mapToVueType($f['type']),
                'required' => $f['required'],
            ];
            if (isset($f['options'])) {
                $item['options'] = $f['options'];
                // Si es select, aseguramos type select
                $item['type'] = 'select';
            }
            if ($f['type'] === 'array' && isset($f['fields'])) {
                $item['fields'] = (new self($f['fields']))->toVueSchema();
            }
            $vue[] = $item;
        }
        return $vue;
    }

    /**
     * Generates a schema formatted for integration with React components.
     *
     * This method prepares the schema data structure so it can be easily consumed
     * by React-based front-end applications, enabling dynamic rendering of forms
     * or interfaces based on the schema definition.
     *
     * @return array The schema array structured for React usage.
     */
    public function toReactSchema(): array
    {
        $react = [];
        foreach ($this->schema as $f) {
            $type = $this->mapToReactComponent($f['type']);
            // Si tiene options, es SelectField
            if (isset($f['options'])) {
                $type = 'SelectField';
            }
            $item = [
                'name'       => $f['name'],
                'label'      => $f['label'],
                'component'  => $type,
                'validation' => $f['required'] ? ['required'] : [],
            ];
            if (isset($f['options'])) {
                $item['options'] = $f['options'];
            }
            if ($f['type'] === 'array' && isset($f['fields'])) {
                $item['fields'] = (new self($f['fields']))->toReactSchema();
            }
            $react[] = $item;
        }
        return $react;
    }

    /**
     * Guesses the field type from the validation rules.
     *
     * @param  array  $rules
     * @return string
     */
    public static function guessType(array $rules): string
    {
        $ruleString = Str::lower(implode('|', $rules));

        if (preg_match('/(?:^|\|)in:/', $ruleString) || Str::contains($ruleString, 'exists:')) {
            return 'select';
        }

        return match (true) {
            Str::contains($ruleString, 'boolean') => 'checkbox',
            Str::contains($ruleString, 'array')   => 'array',
            Str::contains($ruleString, 'integer') => 'integer',
            Str::contains($ruleString, 'numeric') => 'decimal',
            Str::contains($ruleString, 'string')  => 'string',
            default => 'string',
        };
    }

    /**
     * Maps a given type to its corresponding Vue input type.
     *
     * @param string $type The type to map (e.g., 'string', 'integer', 'decimal', 'select', 'array', 'checkbox').
     * @return string The Vue input type (e.g., 'text', 'number', 'select', 'array', 'checkbox').
     */
    private function mapToVueType(string $type): string
    {
        return match ($type) {
            'string'   => 'text',
            'integer', 'decimal' => 'number',
            'select'   => 'select',
            'array'    => 'array',
            'checkbox'=> 'checkbox',
            default    => 'text',
        };
    }

    /**
     * Maps a given type to its corresponding React component name.
     *
     * @param string $type The type to map (e.g., 'TextField', 'string', 'integer', 'decimal', 'array', 'checkbox').
     * @return string The React component name (e.g., 'TextField', 'NumberField', 'ArrayField', 'CheckboxField').
     */
    private function mapToReactComponent(string $type): string
    {
        return match ($type) {
            'TextField', 'string'   => 'TextField',
            'integer', 'decimal' => 'NumberField',
            'array'    => 'ArrayField',
            'checkbox'=> 'CheckboxField',
            default    => 'TextField',
        };
    }
}