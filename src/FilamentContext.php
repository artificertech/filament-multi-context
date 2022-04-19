<?php

namespace Artificertech\FilamentMultiContext;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class FilamentContext
{
    protected static ?string $domain = null;

    protected static ?array $baseMiddleware = null;

    protected static ?array $authMiddleware = null;

    protected static ?string $path = null;

    protected static string | array $middlewares = [];

    protected static ?string $slug = null;

    protected static ?string $prefix = null;

    public static function getDomain()
    {
        return static::$domain ?? config("filament.domain");
    }

    public static function getBaseMiddleware(): array
    {
        return static::$baseMiddleware ?? config("filament.middleware.base");
    }

    public static function getAuthMiddleware(): array
    {
        return static::$authMiddleware ?? config("filament.middleware.auth");
    }

    public static function getPath(): string
    {
        return static::$path ?? config("filament.path");
    }

    public static function getMiddlewares(): string | array
    {
        return static::$middlewares;
    }

    public function getRoutes(): Closure
    {
        return function () {
            $slug = static::getSlug();

            $prefix = static::getPrefix();

            Route::name("{$slug}.")
                ->prefix($prefix)
                ->group(function () {
                    foreach ($this->getPages() as $page) {
                        Route::name('pages.')->group($page::getRoutes());
                    }

                    foreach (static::getResources() as $resource) {
                        Route::name('resources.')->group($resource::getRoutes());
                    }
                });
        };
    }

    public static function getSlug(): string
    {
        return static::$slug ?? (string) Str::of(class_basename(static::class))
            ->replace('Context', '')
            ->kebab()
            ->slug();
    }

    public static function getPrefix(): string
    {
        return static::$prefix ?? static::getSlug();
    }

    public static function getResourcesPath(): string
    {
        $reflection = new ReflectionClass(static::class);

        return dirname($reflection->getFileName()) . "/Resources";
    }

    public static function getResourcesNamespace(): string
    {
        $reflection = new ReflectionClass(static::class);

        return $reflection->getNamespaceName() . "\\Resources";
    }

    public static function getPagesPath(): string
    {
        $reflection = new ReflectionClass(static::class);

        return dirname($reflection->getFileName()) . "/Pages";
    }

    public static function getPagesNamespace(): string
    {
        $reflection = new ReflectionClass(static::class);

        return $reflection->getNamespaceName() . "\\Pages";
    }
}
