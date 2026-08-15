<?php

namespace Combindma\FacebookPixel;

use Combindma\FacebookPixel\Components\Body;
use Combindma\FacebookPixel\Components\Head;
use Composer\InstalledVersions;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
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
        $packagePath = realpath(__DIR__.'/..');

        if (! $this->app->runningInConsole() || $packagePath === false || $this->composerRootPath() !== $packagePath) {
            return;
        }

        Event::listen(CommandStarting::class, function (CommandStarting $event) use ($packagePath): void {
            if (Str::startsWith($event->command, 'boost:')) {
                $this->app->setBasePath($packagePath);
                $this->app->useAppPath($packagePath.'/src');

                $this->app['config']->set(
                    'boost.code_environments.claude_code.guidelines_path',
                    $packagePath.'/CLAUDE.md'
                );
            }
        });
    }

    protected function composerRootPath(): string|false
    {
        return realpath(InstalledVersions::getRootPackage()['install_path']);
    }
}
