<?php

namespace Aphpi\Template\Tests\Endpoints;

use Aphpi\Template\Api;

class ExampleTest extends \Aphpi\Template\Tests\TestCase
{
    protected $api;

    /**
     * @test
     */
    public function it_works()
    {
        $data = $this->api->example->post();
        $this->assertEquals('https://httpbin.org/post', $data['url']);
    }

}