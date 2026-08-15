<?php

use Combindma\FacebookPixel\FacebookPixelServiceProvider;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;

it('registers the Boost listener when developing the package', function () {
    Event::shouldReceive('listen')
        ->once()
        ->with(CommandStarting::class, Mockery::type(Closure::class));

    (new FacebookPixelServiceProvider($this->app))->packageBooted();
});

it('does not register the Boost listener when installed in an application', function () {
    Event::shouldReceive('listen')->never();

    $provider = new class($this->app) extends FacebookPixelServiceProvider
    {
        protected function composerRootPath(): string|false
        {
            return realpath(sys_get_temp_dir());
        }
    };

    $provider->packageBooted();
});
