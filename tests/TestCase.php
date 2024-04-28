<?php

namespace Combindma\FacebookPixel\Tests;

use Combindma\FacebookPixel\FacebookPixelServiceProvider;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use InteractsWithViews;

    protected function getPackageProviders($app): array
    {
        return [
            FacebookPixelServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('meta-pixel.pixel_id', 'pixel_id');
        $app['config']->set('meta-pixel.session_key', 'session_key');
        $app['config']->set('meta-pixel.enabled', true);
        $app['config']->set('meta-pixel.advanced_matching_enabled', true);
    }
}
