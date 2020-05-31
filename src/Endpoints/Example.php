<?php

namespace Aphpi\Template\Endpoints;

use Aphpi\Template\Endpoints\AbstractApi;

class Example extends AbstractApi
{
    public function post() : array
    {
        return $this->client->post('post');
    }

    public function json(array $data) : array
    {
        return $this->client->postJson('post', $data);
    }

    public function form(array $data) : array
    {
        return $this->client->postFormParams('post', $data);
    }
}