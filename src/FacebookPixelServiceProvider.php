<?php

namespace Combindma\FacebookPixel;

use Combindma\FacebookPixel\Components\Body;
use Combindma\FacebookPixel\Components\Head;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FacebookPixelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('meta-pixel')
            ->hasConfigFile('meta-pixel')
            ->hasViewComponents(
                'metapixel',
                Head::class,
                Body::class
            )
            ->hasViews();
    }

    public function registeringPackage(): void
    {
        $this->app->singleton(MetaPixel::class, fn (): MetaPixel => new MetaPixel);
    }

    public function packageBooted(): void
    {
        // Fixing Paths for Laravel Boost Commands
        Event::listen(CommandStarting::class, function ($event) {
            if (str_starts_with($event->command, 'boost:')) {
                app()->setBasePath(realpath(__DIR__.'/..'));
                app()->useAppPath(base_path('src'));

                config()->set('boost.code_environments.claude_code.guidelines_path', base_path('CLAUDE.md'));
            }
        });
    }
}
