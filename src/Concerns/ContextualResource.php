<?php

namespace Artificertech\FilamentMultiContext\Concerns;

trait ContextualResource
{
    protected static ?string $context;

    public static function getContext(): ?string
    {
        return static::$context;
    }

    public static function setContext($context): void
    {
        static::$context = $context;
    }

    public static function getRouteBaseName(): string
    {
        $context = (static::getContext())::getSlug();

        $slug = static::getSlug();

        return "filament.{$context}.resources.{$slug}";
    }
}
