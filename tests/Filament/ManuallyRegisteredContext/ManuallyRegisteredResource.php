<?php

namespace Artificertech\FilamentMultiContext\Tests\Filament\ManuallyRegisteredContext;

use Artificertech\FilamentMultiContext\Concerns\ContextualResource;
use Filament\Resources\Resource;

class ManuallyRegisteredResource extends Resource
{
    use ContextualResource;
}
