<?php

use Artificertech\FilamentMultiContext\Tests\Filament\ManuallyRegisteredContext;
use Artificertech\FilamentMultiContext\Tests\Filament\ManuallyRegisteredContext\ManuallyRegisteredPage;
use Filament\Facades\Filament;

it('can manually register pages', function () {
    Filament::getContexts();

    $this->assertEquals(ManuallyRegisteredContext::class, ManuallyRegisteredPage::getContext());
});
