<?php

use Combindma\FacebookPixel\FacebookPixel;
use Combindma\FacebookPixel\ScriptViewCreator;
use Illuminate\View\View;

beforeEach(function () {
    // Mock the FacebookPixel class
    $this->facebookPixel = Mockery::mock(FacebookPixel::class);
    $this->scriptViewCreator = new ScriptViewCreator($this->facebookPixel);
});

test('it throws an exception when Facebook Pixel is enabled and pixelId is empty', function () {
    $this->facebookPixel->shouldReceive('isEnabled')->andReturn(true);
    $this->facebookPixel->shouldReceive('pixelId')->andReturn('');

    $view = Mockery::mock(View::class);

    expect(fn () => $this->scriptViewCreator->create($view))->toThrow(Exception::class);
});

test('it throws an exception when Facebook Pixel is enabled and sessionKey is empty', function () {
    $this->facebookPixel->shouldReceive('isEnabled')->andReturn(true);
    $this->facebookPixel->shouldReceive('pixelId')->andReturn('1234567890');
    $this->facebookPixel->shouldReceive('sessionKey')->andReturn('');

    $view = Mockery::mock(View::class);

    expect(fn () => $this->scriptViewCreator->create($view))->toThrow(Exception::class);
});
