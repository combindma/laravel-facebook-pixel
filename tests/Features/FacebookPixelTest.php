<?php

use Combindma\FacebookPixel\Facades\FacebookPixel;

it('can test if config file are set', function () {
    $this->assertEquals('facebook_pixel_id', $this->facebookPixel->pixelId());
    $this->assertEquals('sessionKey', $this->facebookPixel->sessionKey());
    $this->assertTrue($this->facebookPixel->isEnabled());
});

it('can track an event', function () {
    FacebookPixel::track('Purchase', ['currency' => 'USD', 'value' => 30.00]);
    expect(FacebookPixel::getEventLayer()->toArray())->toBe([
        'Purchase' => [
            'currency' => 'USD', 'value' => 30.00,
        ],
    ]);
});

it('can set a new pixel id', function () {
    FacebookPixel::setPixelId('new_pixel_id');
    expect(FacebookPixel::pixelId())->toBe('new_pixel_id');
});

it('can enable or disable on the fly', function () {
    FacebookPixel::disable();
    expect(FacebookPixel::isEnabled())->toBe(false);
    FacebookPixel::enable();
    expect(FacebookPixel::isEnabled())->toBe(true);
});

it('can clear all event layer', function () {
    FacebookPixel::track('Purchase', ['currency' => 'USD', 'value' => 30.00]);
    FacebookPixel::clear();
    expect(FacebookPixel::getEventLayer()->toArray())->toBe([]);
});
