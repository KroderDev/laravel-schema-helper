<?php

use Kroderdev\SchemaHelper\Tests\Stubs\ExampleFormRequest;
use Orchestra\Testbench\TestCase;

class RoutesTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [Kroderdev\SchemaHelper\SchemaServiceProvider::class];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('schema-helper.schemas', [
            'example' => ExampleFormRequest::class,
        ]);
    }

    /** @test */
    public function it_returns_available_schemas()
    {
        $response = $this->getJson('/api/schemas');

        $response->assertStatus(200);
        $response->assertJson([
            'schemas' => ['example'],
        ]);
    }
}