<?php

if (! function_exists('facebookPixel')) {
    function facebookPixel(): \Combindma\FacebookPixel\FacebookPixel
    {
        return app('facebookPixel');
    }
}

if (! function_exists('facebookPixelHead')) {
    function facebookPixelHead(): string
    {
        return facebookPixel()->headContent();
    }
}

if (! function_exists('facebookPixelBody')) {
    function facebookPixelBody(): string
    {
        return facebookPixel()->bodyContent();
    }
}
