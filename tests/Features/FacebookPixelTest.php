<?php

namespace Combindma\FacebookPixel\Tests\Features;

use Combindma\FacebookPixel\Tests\TestCase;

class FacebookPixelTest extends TestCase
{
    public function test_headContent()
    {
        $this->assertNotEmpty($this->facebookPixel->headContent());
        $this->assertNotEmpty(facebookPixelHead());
    }

    public function test_bodyContent()
    {
        $this->facebookPixel->createEvent('viewContent', ['content_type' => 'product',]);
        $this->assertEquals("<script>fbq('track', 'viewContent', {\"content_type\":\"product\"});</script>", $this->facebookPixel->bodyContent());
        $this->facebookPixel->createEvent('viewContent', ['content_type' => 'product',]);
        $this->assertEquals("<script>fbq('track', 'viewContent', {\"content_type\":\"product\"});</script>", facebookPixelBody());
    }
}
