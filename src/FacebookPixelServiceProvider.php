<?php

namespace Combindma\FacebookPixel;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FacebookPixelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-facebook-pixel')
            ->hasConfigFile();
    }

    public function registeringPackage()
    {
        $this->app->singleton('facebookPixel', function () {
            return new FacebookPixel();
        });
    }
}
