<?php

use Combindma\FacebookPixel\Facades\MetaPixel;
use Combindma\FacebookPixel\MetaPixel as FacebookPixelService;

test('it resolves the correct underlying class from the facade', function () {
    $resolvedClass = MetaPixel::getFacadeRoot();
    expect($resolvedClass)->toBeInstanceOf(FacebookPixelService::class);
});
