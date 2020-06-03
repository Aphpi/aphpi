<?php

namespace Aphpi\Template\Endpoints;

use Aphpi\Template\Client;
use Aphpi\Template\Traits\HasAttributes;

abstract class Endpoint
{
    use HasAttributes;

    protected $client;

    public function __construct(Client $client, array $attributes = [])
    {
        $this->client = $client;
        $this->setAttributes($attributes);
        $this->setEndpoints();
    }

    protected function setEndpoints() : void
    {
        //
    }

    public function snapshot() : self
    {
        $this->client->snapshot();

        return $this;
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function __call($name, $arguments)
    {
        $this->setAttribute($name, $arguments[0]);
        $this->setEndpoints();

        return $this;
    }
}