<?php

namespace Aphpi\Template;

use Aphpi\Client\Client as BaseClient;
use Aphpi\Template\Client;
use Aphpi\Template\Endpoints\Example;
use Aphpi\Template\Traits\HasAttributes;

class Api
{
    use HasAttributes;

    public $example;

    protected $client;

    protected function getClient() : BaseClient
    {
        return new Client([
            'base_uri' => 'https://httpbin.org',
            'timeout'  => 2.0,
        ], $this->attributes);
    }

    protected function setEndpoints(BaseClient $client) : void
    {
        $this->example = new Example($client);
    }

    public function __construct(array $attributes = [])
    {
        $this->setAttributes($attributes);
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

    public function __get($key)
    {
        return $this->client->getAttribute($key);
    }

    public function __set($key, $value)
    {
        $this->client->setAttribute($key, $value);
    }
}