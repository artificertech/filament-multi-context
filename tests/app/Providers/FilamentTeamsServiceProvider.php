<?php

namespace Artificertech\FilamentMultiContext\Tests\App\Providers;

use Artificertech\FilamentMultiContext\ContextServiceProvider;

class FilamentTeamsServiceProvider extends ContextServiceProvider
{
    public static string $name = 'filament-teams';

    protected function getRenderHooks(): array
    {
        return [
            'test' => static fn (): string => 'something',
        ];
    }
}
