<?php

namespace Artificertech\FilamentMultiContext\Tests;

use Artificertech\FilamentMultiContext\FilamentMultiContextServiceProvider;
use Filament\FilamentServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            FilamentMultiContextServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        config()->set('filament.pages', [
            'namespace' => 'Artificertech\\FilamentMultiContext\\Tests\\Filament\\Pages',
            'path' => __DIR__ . '/Filament/Pages',
            'register' => [],
        ]);

        config()->set('filament-multi-context.contexts', [
            'namespace' => 'Artificertech\\FilamentMultiContext\\Tests\\Filament',
            'path' => __DIR__ . '/Filament',
            'view_path' => resource_path('views/filament'),
            'register' => [],
        ]);
    }
}
