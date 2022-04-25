<?php

use Artificertech\FilamentMultiContext\Http\Middleware\ApplyContext;
use Artificertech\FilamentMultiContext\Tests\Filament\AdminContext;
use Artificertech\FilamentMultiContext\Tests\Filament\SettingsContext;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

it('registers ApplyContext middleware', function () {
    $this->assertTrue(in_array(
        ApplyContext::class . ':' . AdminContext::class,
        Route::getRoutes()->getByName('filament.admin-context.pages.dashboard')->middleware()
    ));

    $this->assertTrue(in_array(
        ApplyContext::class . ':' . SettingsContext::class,
        Route::getRoutes()->getByName('filament.settings-context.pages.dashboard')->middleware()
    ));
});

it('applys context to routes', function () {
    $response = $this->get('admin-context/dashboard');

    $this->assertEquals('admin-context', Filament::context());

    $response = $this->get('settings-context/dashboard');

    $this->assertEquals('settings-context', Filament::context());
});
