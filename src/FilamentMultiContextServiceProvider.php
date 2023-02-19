<?php

namespace Artificertech\FilamentMultiContext;

use Artificertech\FilamentMultiContext\Commands\MakeContextCommand;
use Artificertech\FilamentMultiContext\Commands\MakePageCommand;
use Artificertech\FilamentMultiContext\Commands\MakeRelationManagerCommand;
use Artificertech\FilamentMultiContext\Commands\MakeResourceCommand;
use Artificertech\FilamentMultiContext\Commands\MakeWidgetCommand;
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
        $this->app->extend('filament', static function ($service) {
            return new FilamentMultiContextManager($service);
        });

        $this->app->extend(\Filament\Commands\MakeResourceCommand::class, static function () {
            return new MakeResourceCommand();
        });
        $this->app->extend(\Filament\Commands\MakePageCommand::class, static function () {
            return new MakePageCommand();
        });
        $this->app->extend(\Filament\Commands\MakeRelationManagerCommand::class, static function () {
            return new MakeRelationManagerCommand();
        });
        $this->app->extend(\Filament\Commands\MakeWidgetCommand::class, static function () {
            return new MakeWidgetCommand();
        });
    }

    public function packageBooted(): void
    {
        Livewire::addPersistentMiddleware([
            ApplyContext::class,
        ]);
    }
}
