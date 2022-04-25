<?php

namespace Artificertech\FilamentMultiContext;

use Artificertech\FilamentMultiContext\Commands\MakeContextCommand;
use Artificertech\FilamentMultiContext\Http\Middleware\ApplyContext;
use Filament\Commands as FilamentCommands;
use Filament\Facades\Filament;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Livewire\Livewire;
use ReflectionClass;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\Finder\SplFileInfo;

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
            ->hasConfigFile()
            ->hasCommand(MakeContextCommand::class)
            ->hasViews()
            ->hasRoutes(['web']);
    }

    public function packageRegistered(): void
    {
        $this->app->extend('filament', function ($service, $app) {
            return new FilamentMultiContextManager($service);
        });

        $this->app->resolving('filament', function ($manager, $app) {
            $this->discoverContexts();
        });

        // $this->app->extend(FilamentCommands\MakePageCommand::class, function (FilamentCommands\MakePageCommand $command, $app) {
        //     return new Commands\MakePageCommand();
        // });
    }

    public function packageBooted(): void
    {
        Livewire::addPersistentMiddleware([
            ApplyContext::class,
        ]);
    }

    protected function discoverContexts()
    {
        $filesystem = app(Filesystem::class);

        Filament::registerContexts(config('filament-multi-context.contexts.register', []));

        if (!$filesystem->exists(config('filament-multi-context.contexts.path'))) {
            return;
        }

        Filament::registerContexts(collect($filesystem->allFiles(config('filament-multi-context.contexts.path')))
            ->map(function (SplFileInfo $file): string {
                return (string) Str::of(config('filament-multi-context.contexts.namespace'))
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(fn (string $class): bool => is_subclass_of($class, ContextManager::class) && (!(new ReflectionClass($class))->isAbstract()))
            ->toArray());
    }
}
