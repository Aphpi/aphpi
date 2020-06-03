<?php

namespace Aphpi\Template\Tests\Endpoints;

use Aphpi\Client\Client as BaseClient;
use Aphpi\Template\Api;
use Aphpi\Template\Endpoints\Endpoint;

class ExampleTest extends \Aphpi\Template\Tests\TestCase
{
    /**
     * @test
     */
    public function it_works()
    {
        $data = $this->api->example->post();
        $this->assertEquals('https://httpbin.org/post', $data['url']);
    }

    /**
     * @test
     */
    public function it_can_set_attributes()
    {
        $this->assertNull($this->api->example->foo);

        $this->api->example->foo = 'bar';

        $attributes = $this->api->example->getAttributes();

        $this->assertEquals('bar', $attributes['foo']);
        $this->assertEquals('bar', $this->api->example->foo);
    }

    /**
     * @test
     */
    public function it_can_set_attributes_by_methods()
    {
        $value = 5;

        $this->assertEquals($this->api->example->id($value), $this->api->example);
        $this->assertEquals($value, $this->api->example->id);
        $this->assertEquals($value, $this->api->example->getAttribute('id'));
    }

    /**
     * @test
     */
    public function it_can_pass_attributes()
    {
        $TestApi = new TestApi();

        $value = 7;
        $response = $TestApi->FooEndpoint->example_id($value)->BarEndpoint->get();
        $this->assertArrayHasKey('example_id', $response['args']);
        $this->assertEquals($value, $response['args']['example_id']);

        $value = 5;
        $response = $TestApi->FooEndpoint->example_id($value)->BarEndpoint->get();
        $this->assertArrayHasKey('example_id', $response['args']);
        $this->assertEquals($value, $response['args']['example_id']);
    }

}

class TestApi extends Api
{
    public $FooEndpoint;

    protected function setEndpoints(BaseClient $client) : void
    {
        $this->FooEndpoint = new FooEndpoint($client, $this->attributes);
    }
}

class FooEndpoint extends Endpoint
{
    public $BarEndpoint;

    protected function setEndpoints() : void
    {
        $this->BarEndpoint = new BarEndpoint($this->client, $this->attributes);
    }
}

class BarEndpoint extends Endpoint
{
    public function get() : array
    {
        return $this->client->get('get', [
            'example_id' => $this->example_id
        ]);
    }
}