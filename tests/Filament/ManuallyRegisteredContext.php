<?php

namespace Artificertech\FilamentMultiContext\Tests\Filament;

use Artificertech\FilamentMultiContext\ContextManager;
use Artificertech\FilamentMultiContext\Tests\Filament\ManuallyRegisteredContext\ManuallyRegisteredPage;
use Artificertech\FilamentMultiContext\Tests\Filament\ManuallyRegisteredContext\ManuallyRegisteredResource;

class ManuallyRegisteredContext extends ContextManager
{
    protected array $pages = [
        ManuallyRegisteredPage::class,
    ];

    protected array $resources = [
        ManuallyRegisteredResource::class,
    ];
}
