<?php

namespace Aphpi\Template\Traits;

trait HasAttributes
{
    protected $attributes = [];

    public function getAttributes() : array
    {
        return $this->attributes;
    }

    public function getAttribute($key)
    {
        if (! array_key_exists($key, $this->attributes)) {
            return null;
        }

        return $this->attributes[$key];
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }
}