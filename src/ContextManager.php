<?php

namespace Artificertech\FilamentMultiContext;

use Closure;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\FilamentManager;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class ContextManager extends FilamentManager
{
    protected static ?string $domain = null;

    protected static ?array $baseMiddleware = null;

    protected static ?array $authMiddleware = null;

    protected static ?string $path = null;

    protected static string | array $middlewares = [];

    protected static ?string $slug = null;

    protected static ?string $prefix = null;

    protected static ?string $auth = null;

    protected static ?string $defaultAvatarProvider = null;

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

    public static function getSlug(): string
    {
        return static::$slug ?? (string) Str::of(class_basename(static::class))
            ->kebab()
            ->slug();
    }

    public static function getPrefix(): string
    {
        return static::$prefix ?? (string) Str::of(static::getSlug())->replace('-context', '');
    }

    public static function getResourcesPath(): string
    {
        $reflection = new ReflectionClass(static::class);

        return dirname($reflection->getFileName()) . '/' . class_basename($reflection->getName()) . "/Resources";
    }

    public static function getResourcesNamespace(): string
    {
        $reflection = new ReflectionClass(static::class);

        return $reflection->getNamespaceName() . '\\' . class_basename($reflection->getName()) . "\\Resources";
    }

    public static function getPagesPath(): string
    {
        $reflection = new ReflectionClass(static::class);

        return dirname($reflection->getFileName()) . '/' . class_basename($reflection->getName()) . "/Pages";
    }

    public static function getPagesNamespace(): string
    {
        $reflection = new ReflectionClass(static::class);

        return $reflection->getNamespaceName() . '\\' . class_basename($reflection->getName()) . "\\Pages";
    }

    public static function getWidgetsPath(): string
    {
        $reflection = new ReflectionClass(static::class);

        return dirname($reflection->getFileName()) . '/' . class_basename($reflection->getName()) . "/Widgets";
    }

    public static function getWidgetsNamespace(): string
    {
        $reflection = new ReflectionClass(static::class);

        return $reflection->getNamespaceName() . '\\' . $reflection->getName() . "\\Widgets";
    }

    public static function getAuth(): ?Guard
    {
        return static::$auth ? auth()->guard(static::$auth) : null;
    }

    public static function getDefaultAvatarProvider(): ?AvatarProvider
    {
        return static::$defaultAvatarProvider;
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

                    foreach ($this->getResources() as $resource) {
                        Route::name('resources.')->group($resource::getRoutes());
                    }
                });
        };
    }

    public function auth(): Guard
    {
        return static::getAuth() ?? auth()->guard(config('filament.auth.guard'));
    }

    public function getUserAvatarUrl(Model|Authenticable $user): string
    {
        $avatar = null;

        if ($user instanceof HasAvatar) {
            $avatar = $user->getFilamentAvatarUrl();
        }

        if ($avatar) {
            return $avatar;
        }

        $provider = static::getDefaultAvatarProvider() ?? config('filament.default_avatar_provider');

        return app($provider)->get($user);
    }

    public function setResourceContexts()
    {
        foreach ($this->resources as $resourceClass) {
            $resourceClass::setContext(static::class);
        }
    }

    public function setPageContexts()
    {
        foreach ($this->pages as $pageClass) {
            $pageClass::setContext(static::class);
        }
    }
}
