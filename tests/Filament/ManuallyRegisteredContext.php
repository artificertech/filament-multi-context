<?php

namespace Artificertech\FilamentMultiContext\Tests\Filament;

use Artificertech\FilamentMultiContext\ContextManager;
use Artificertech\FilamentMultiContext\Tests\Filament\ManuallyRegisteredContext\ManuallyRegisteredPage;

class ManuallyRegisteredContext extends ContextManager
{
    protected array $pages = [
        ManuallyRegisteredPage::class
    ];
}
