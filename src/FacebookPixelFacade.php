<?php

namespace Combindma\FacebookPixel;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Combindma\FacebookPixel\FacebookPixel
 */
class FacebookPixelFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'facebook-pixel';
    }
}
