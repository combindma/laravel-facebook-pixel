<?php

use Combindma\FacebookPixel\Facades\FacebookPixel;
use Combindma\FacebookPixel\FacebookPixel as FacebookPixelService;

test('it resolves the correct underlying class from the facade', function () {
    $resolvedClass = FacebookPixel::getFacadeRoot();
    expect($resolvedClass)->toBeInstanceOf(FacebookPixelService::class);
});
