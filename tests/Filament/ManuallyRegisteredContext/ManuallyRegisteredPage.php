<?php

namespace Artificertech\FilamentMultiContext\Tests\Filament\ManuallyRegisteredContext;

use Artificertech\FilamentMultiContext\Concerns\ContextualPage;
use Filament\Pages\Page;

class ManuallyRegisteredPage extends Page
{
    use ContextualPage;
}
