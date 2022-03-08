<?php

namespace Combindma\FacebookPixel\Tests;

use Combindma\FacebookPixel\FacebookPixel;
use Combindma\FacebookPixel\FacebookPixelServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public $facebookPixel;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            FacebookPixelServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('facebook-pixel.facebook_pixel_id', 'facebook_pixel_id');
        $app['config']->set('facebook-pixel.sessionKey', 'sessionKey');
        $app['config']->set('facebook-pixel.enabled', true);
        $this->facebookPixel = new FacebookPixel();
    }
}
