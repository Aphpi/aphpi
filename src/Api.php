<?php

namespace Aphpi\Template;

use Aphpi\Client\Client as BaseClient;
use Aphpi\Template\Endpoints\Example;
use Aphpi\Template\Client;

class Api
{
    protected $client;

    protected function getClient() : BaseClient
    {
        return new Client([
            'base_uri' => 'https://httpbin.org',
            'timeout'  => 2.0,
        ]);
    }

    protected function setEndpoints(BaseClient $client) : void
    {
        $this->example = new Example($client);
    }

    public function __construct()
    {
        $this->setUp();
    }

    protected function setUp() : void
    {
        $this->registerEndpoints();
    }

    protected function registerEndpoints()
    {
        $client = $this->getClient();

        $this->setEndpoints($client);

        return $this;
    }

}