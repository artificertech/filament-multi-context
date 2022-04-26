<?php

namespace Artificertech\FilamentMultiContext;

use Artificertech\FilamentMultiContext\Concerns\ContextualPage;
use Artificertech\FilamentMultiContext\Concerns\ContextualResource;
use Exception;
use Filament\FilamentManager;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class FilamentMultiContextManager
{
    use ForwardsCalls;

    /**
     * @var \Filament\FilamentManager
     */
    protected $filament;

    protected array $contexts = [];

    protected array $contextSlugs = [];

    protected ?string $context = null;

    public function __construct(FilamentManager $filament)
    {
        $this->filament = $filament;
    }

    public function registerContexts($contexts): void
    {
        if (! is_array($contexts)) {
            $contexts = [$contexts];
        }

        $contexts = collect($contexts)->mapWithKeys(function ($context) {
            if (is_string($context)) {
                $context = new $context();
            }

            if (! is_a($context, ContextManager::class)) {
                throw new Exception('Global search provider ' . $context::class . ' is not an instance of ' . ContextManager::class);
            }

            $this->discoverResourcesForContext($context);
            $this->discoverPagesForContext($context);
            $this->discoverWidgetsForContext($context);

            $this->contextSlugs[$context::class] = $context::getSlug();

            return [$context::class => $context];
        })->toArray();

        $this->contexts = array_merge($this->contexts, $contexts);
    }

    public function getContext($contextClass = null)
    {
        if (is_null($this->context)) {
            return $this->filament;
        }

        return $this->getContexts()[$contextClass];
    }

    public function getContexts(): array
    {
        return $this->contexts;
    }

    public function setCurrentContext(string $context): void
    {
        $this->context = $context;
    }

    public function context($returnObject = false): null|string|FilamentManager
    {
        if (is_null($this->context) || ! array_key_exists($this->context, $this->contexts)) {
            return $this->filament;
        }

        return $returnObject ? $this->contexts[$this->context] : $this->contextSlugs[$this->context];
    }

    protected function discoverResourcesForContext(ContextManager $context)
    {
        $filesystem = app(Filesystem::class);

        if (! $filesystem->exists($context::getResourcesPath())) {
            return;
        }

        $context->registerResources(collect($filesystem->allFiles($context::getResourcesPath()))
            ->map(function (SplFileInfo $file) use ($context): string {
                return (string) Str::of($context::getResourcesNamespace())
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(fn (string $class): bool => is_subclass_of($class, Resource::class) && (! (new ReflectionClass($class))->isAbstract()) && in_array(ContextualResource::class, class_uses_recursive($class)))
            ->each(function (string $resource) use ($context) {
                $resource::setContext($context::class);
            })
            ->toArray());
    }

    protected function discoverPagesForContext(ContextManager $context)
    {
        $filesystem = app(Filesystem::class);

        if (! $filesystem->exists($context::getPagesPath())) {
            return;
        }

        $context->registerPages(collect($filesystem->allFiles($context::getPagesPath()))
            ->map(function (SplFileInfo $file) use ($context): string {
                return (string) Str::of($context::getPagesNamespace())
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(fn (string $class): bool => is_subclass_of($class, Page::class) && (! (new ReflectionClass($class))->isAbstract()) && in_array(ContextualPage::class, class_uses_recursive($class)))
            ->each(function (string $page) use ($context) {
                $page::setContext($context::class);
            })
            ->toArray());
    }

    protected function discoverWidgetsForContext(ContextManager $context)
    {
        $filesystem = app(Filesystem::class);

        if (! $filesystem->exists($context::getWidgetsPath())) {
            return;
        }

        $context->registerWidgets(collect($filesystem->allFiles($context::getWidgetsPath()))
            ->map(function (SplFileInfo $file) use ($context): string {
                return (string) Str::of($context::getWidgetsNamespace())
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(fn (string $class): bool => is_subclass_of($class, Widget::class) && (! (new ReflectionClass($class))->isAbstract()))
            ->toArray());
    }

    /**
     * Dynamically handle calls into the filament instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $response = $this->forwardCallTo($this->context(true), $method, $parameters);

        if ($response instanceof FilamentManager) {
            return $this;
        }

        return $response;
    }
}
