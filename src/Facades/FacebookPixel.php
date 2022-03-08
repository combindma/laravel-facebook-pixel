<?php

namespace Combindma\FacebookPixel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Combindma\FacebookPixel\FacebookPixel
 */
class FacebookPixel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Combindma\FacebookPixel\FacebookPixel::class;
    }
}
