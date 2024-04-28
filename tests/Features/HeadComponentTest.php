<?php

use Combindma\FacebookPixel\Components\Head;

it('throws an exception when Meta pixel is enabled and pixelId is empty', function () {
    config()->set('meta-pixel.pixel_id', '');
    $this->component(Head::class);
})->throws('You need to set a Meta Pixel Id in .env file.');

it('throws an exception when Meta Pixel is enabled and sessionKey is empty', function () {
    config()->set('meta-pixel.session_key', '');
    $this->component(Head::class);
})->throws('You need to set a session key for Meta Pixel in .env file.');
