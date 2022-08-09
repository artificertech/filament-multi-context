<?php

namespace Artificertech\FilamentMultiContext;

use Artificertech\FilamentMultiContext\Commands\MakeContextCommand;
use Artificertech\FilamentMultiContext\Http\Middleware\ApplyContext;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMultiContextServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('filament-multi-context')
            ->hasCommand(MakeContextCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->extend('filament', function ($service, $app) {
            return new FilamentMultiContextManager($service);
        });
    }

    public function packageBooted(): void
    {
        Livewire::addPersistentMiddleware([
            ApplyContext::class,
        ]);
    }
}
