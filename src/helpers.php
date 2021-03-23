<?php


use Combindma\FacebookPixel\FacebookPixel;

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

FacebookPixel::macro('addEvent', function ($eventName, $data) {
    facebookPixel()->createEvent($eventName, $data);
});


FacebookPixel::macro('viewContent', function ($data) {
    facebookPixel()->createEvent('ViewContent', $data);
});

FacebookPixel::macro('addToCart', function ($data) {
    facebookPixel()->createEvent('AddToCart', $data);
});

FacebookPixel::macro('initiateCheckout', function ($data) {
    facebookPixel()->createEvent('InitiateCheckout', $data);
});

FacebookPixel::macro('purchase', function ($data) {
    facebookPixel()->createEvent('Purchase', $data);
});
