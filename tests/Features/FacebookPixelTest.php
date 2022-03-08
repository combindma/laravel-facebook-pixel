<?php

it('can test if config file are set', function () {
    $this->assertEquals('facebook_pixel_id', $this->facebookPixel->pixelId());
    $this->assertEquals('sessionKey', $this->facebookPixel->sessionKey());
    $this->assertTrue($this->facebookPixel->isEnabled());
});
