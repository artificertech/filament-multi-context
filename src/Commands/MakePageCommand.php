<?php

namespace Artificertech\FilamentMultiContext\Commands;

use Filament\Commands\MakePageCommand as FilamentMakePageCommand;
use Illuminate\Support\Str;

class MakePageCommand extends FilamentMakePageCommand
{
    public function __construct()
    {
        $this->signature .= ' {--c|context= : The context to create the page in}';

        parent::__construct();
    }

    public function handle(): int
    {
        if (($context = $this->option('context')) === null) {
            return parent::handle();
        }

        $baseContextNamespace = config('filament-multi-context.contexts.namespace');

        $baseContextAppPath = config('filament-multi-context.contexts.app_path');

        $baseContextViewPath = config('filament-multi-context.contexts.view_path');

        $contextNamespace = Str::of($baseContextNamespace)
            ->trim('\\')
            ->trim(' ')
            ->finish('\\')
            ->append("$context");

        $contextAppPath = Str::of($baseContextAppPath)
            ->trim('/')
            ->trim(' ')
            ->replace('/', '\\')
            ->finish('\\')
            ->append("$context\\");


        $contextViewPath = Str::of($baseContextViewPath)
            ->trim('/')
            ->trim(' ')
            ->replace('/', '\\')
            ->finish('\\')
            ->append("$context\\");

        $page = (string) Str::of($this->argument('name') ?? $this->askRequired('Name (e.g. `Settings`)', 'name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $pageClass = (string) Str::of($page)->afterLast('\\');
        $pageNamespace = Str::of($page)->contains('\\') ?
            (string) Str::of($page)->beforeLast('\\') :
            '';

        $resource = null;
        $resourceClass = null;

        $resourceInput = $this->option('resource') ?? $this->ask('(Optional) Resource (e.g. `UserResource`)');

        if ($resourceInput !== null) {
            $resource = (string) Str::of($resourceInput)
                ->studly()
                ->trim('/')
                ->trim('\\')
                ->trim(' ')
                ->replace('/', '\\');

            if (! Str::of($resource)->endsWith('Resource')) {
                $resource .= 'Resource';
            }

            $resourceClass = (string) Str::of($resource)
                ->afterLast('\\');
        }

        $view = Str::of($page)
            ->prepend($resource === null ? 'pages\\' : "resources\\{$resource}\\pages\\")
            ->prepend($contextViewPath)
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) Str::of($page)
                ->prepend($resource === null ? 'Pages\\' : "Resources\\{$resource}\\Pages\\")
                ->prepend($contextAppPath)
                ->replace('\\', '/')
                ->append('.php')
        );

        $viewPath = resource_path(
            (string) Str::of($view)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        if (! $this->option('force') && $this->checkForCollision([
            $path,
            $viewPath,
        ])) {
            return static::INVALID;
        }

        if ($resource === null) {
            $this->copyStubToApp('Page', $path, [
                'class' => $pageClass,
                'namespace' => Str::of("\\Pages" . ($pageNamespace !== '' ? "\\{$pageNamespace}" : ''))->prepend($contextNamespace),
                'view' => $view,
            ]);
        } else {
            $this->copyStubToApp('ResourcePage', $path, [
                'baseResourcePage' => 'Filament\\Resources\\Pages\\Page',
                'baseResourcePageClass' => 'Page',
                'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages" . ($pageNamespace !== '' ? "\\{$pageNamespace}" : ''),
                'resource' => $resource,
                'resourceClass' => $resourceClass,
                'resourcePageClass' => $pageClass,
                'view' => $view,
            ]);
        }

        $this->copyStubToApp('PageView', $viewPath);

        $this->info("Successfully created {$page}!");

        if ($resource !== null) {
            $this->info("Make sure to register the page in `{$resourceClass}::getPages()`.");
        }

        return static::SUCCESS;
    }
}
