<?php

namespace Aphpi\Template\Api;

use Aphpi\Template\Client;

class AbstractApi
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