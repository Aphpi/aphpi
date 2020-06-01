<?php

namespace Aphpi\Template\Endpoints;

use Aphpi\Template\Client;

abstract class Endpoint
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function snapshot() : self
    {
        $this->client->snapshot();

        return $this;
    }
}