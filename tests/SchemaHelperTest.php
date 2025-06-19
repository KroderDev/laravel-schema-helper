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
            'quantity' => 'required|integer|min:1|max:10',
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
        return [Kroderdev\SchemaHelper\SchemaServiceProvider::class];
    }

    public function test_schema_is_generated()
    {
        $schema = SchemaHelper::generateFromRequest(new FakeRequest())->toArray();

        $this->assertIsArray($schema);
        $this->assertCount(4, $schema);
        $this->assertEquals('name', $schema[0]['name']);
        $this->assertEquals('select', $schema[1]['type']);
        $this->assertEquals(['kg' => 'kg', 'g' => 'g'], $schema[1]['options']);
        $this->assertEquals('integer', $schema[2]['type']);
        $this->assertEquals(1, $schema[2]['min']);
        $this->assertEquals(10, $schema[2]['max']);
        $this->assertEquals('array', $schema[3]['type']);
        $this->assertEquals('options', $schema[3]['name']);
    }

    public function test_vue_schema_generation()
    {
        $schema = SchemaHelper::generateFromRequest(new FakeRequest())
            ->toVueSchema();

        $this->assertIsArray($schema);
        $this->assertEquals('select', $schema[1]['type']);
        $this->assertEquals(['kg' => 'kg', 'g' => 'g'], $schema[1]['options']);
        $this->assertEquals('number', $schema[2]['type']);
    }

    public function test_react_schema_generation()
    {
        $schema = SchemaHelper::generateFromRequest(new FakeRequest())
            ->toReactSchema();

        $this->assertIsArray($schema);
        $this->assertEquals('SelectField', $schema[1]['component']);
        $this->assertEquals(['kg' => 'kg', 'g' => 'g'], $schema[1]['options']);
        $this->assertEquals('NumberField', $schema[2]['component']);
    }
}
