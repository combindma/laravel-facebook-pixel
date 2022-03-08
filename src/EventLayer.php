<?php

namespace Combindma\FacebookPixel;

use Illuminate\Support\Arr;

class EventLayer
{
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function set(string $eventName, array $parameters = [])
    {
        $this->data = Arr::add($this->data, $eventName, $parameters);
    }

    public function merge(array $newData)
    {
        $this->data = array_merge($this->data, $newData);
    }

    public function clear()
    {
        $this->data = [];
    }

    public function toArray()
    {
        return $this->data;
    }
}
