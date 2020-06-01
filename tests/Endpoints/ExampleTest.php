<?php

namespace Aphpi\Template\Tests\Endpoints;

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

}