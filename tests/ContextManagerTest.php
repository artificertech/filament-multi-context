<?php

use Artificertech\FilamentMultiContext\Tests\Filament\ManuallyRegisteredContext;
use Artificertech\FilamentMultiContext\Tests\Filament\ManuallyRegisteredContext\ManuallyRegisteredPage;
use Artificertech\FilamentMultiContext\Tests\Filament\ManuallyRegisteredContext\ManuallyRegisteredResource;
use Filament\Facades\Filament;

it('can manually register pages', function () {
    Filament::getContexts();

    $this->assertEquals(ManuallyRegisteredContext::class, ManuallyRegisteredPage::getContext());
});

it('can manually register resources', function () {
    Filament::getContexts();

    $this->assertEquals(ManuallyRegisteredContext::class, ManuallyRegisteredResource::getContext());
});
