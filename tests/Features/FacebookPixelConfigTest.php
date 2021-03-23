<?php

namespace Combindma\FacebookPixel\Tests\Features;

use Combindma\FacebookPixel\Tests\TestCase;

class FacebookPixelConfigTest extends TestCase
{
    public function test_if_config_file_are_set()
    {
        $this->assertEquals('facebook_pixel_id', $this->facebookPixel->pixelId());
        $this->assertEquals('sessionKey', $this->facebookPixel->sessionKey());
        $this->assertTrue($this->facebookPixel->isEnabled());
    }
}
