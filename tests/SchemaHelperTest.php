<?php

use Kroderdev\SchemaHelper\SchemaHelper;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Http\FormRequest;

class FakeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'unit' => 'required|in:kg,g',
            'options' => 'array',
            'options.*.label' => 'required|string',
            'options.*.value' => 'required|numeric',
        ];
    }
}

class SchemaHelperTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [Kroderdev\SchemaHelper\SchemaHelperProvider::class];
    }

    public function test_schema_is_generated()
    {
        $schema = SchemaHelper::generateFromRequest(new FakeRequest())->toArray();

        $this->assertIsArray($schema);
        $this->assertCount(3, $schema);
        $this->assertEquals('name', $schema[0]['name']);
        $this->assertEquals('select', $schema[1]['type']);
        $this->assertEquals('array', $schema[2]['type']);
        $this->assertEquals('options', $schema[2]['name']);
    }
}
