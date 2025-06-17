<?php

namespace Kroderdev\SchemaHelper;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class SchemaHelper
{
    /**
     * Generates a JSON schema from a FormRequest.
     *
     * @param FormRequest  $request  Instance of FormRequest
     * @return SchemaBuilder
     */
    public static function generateFromRequest(FormRequest $request): SchemaBuilder
    {
        $rules = method_exists($request, 'rules')
            ? $request->rules()
            : [];

        $schema = [];
        $nested = [];
        $nestedParents = [];

        // 1) Detect parent fields of nested fields
        foreach ($rules as $field => $validations) {
            if (Str::contains($field, '.*.')) {
                [$parent] = explode('.*.', $field);
                $nestedParents[$parent] = true;
            }
        }

        // 2) Build flat and nested schema
        foreach ($rules as $field => $validations) {
            $rulesArray  = is_array($validations)
                ? $validations
                : explode('|', $validations);
            $ruleString = implode('|', $rulesArray);

            if (Str::contains($field, '.*.')) {
                [$parent, $child] = explode('.*.', $field);

                $nested[$parent]['type']     = 'array';
                $nested[$parent]['label']    = ucfirst($parent);
                $nested[$parent]['required'] = true;
                $nested[$parent]['fields'][] = [
                    'name'     => $child,
                    'type'     => SchemaBuilder::guessType($rulesArray),
                    'required' => Str::contains($ruleString, 'required'),
                    'label'    => ucfirst($child),
                ];
            } else {
                // Skip flat definition if it is a nested parent
                if (isset($nestedParents[$field])) {
                    continue;
                }

                $schema[] = [
                    'name'     => $field,
                    'type'     => SchemaBuilder::guessType($rulesArray),
                    'required' => Str::contains($ruleString, 'required'),
                    'label'    => ucfirst(str_replace('_', ' ', $field)),
                ];
            }
        }

        // 3) Merge nested arrays at the end
        foreach ($nested as $name => $structure) {
            $schema[] = array_merge(['name' => $name], $structure);
        }

        return new SchemaBuilder($schema);
    }
}