<?php

namespace Artificertech\FilamentMultiContext\Tests\Filament;

use Artificertech\FilamentMultiContext\ContextManager;

class AdminContext extends ContextManager
{
    protected static ?string $path = '';

    protected static ?string $prefix = 'admin-context';
}
