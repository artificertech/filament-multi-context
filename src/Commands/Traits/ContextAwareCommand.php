<?php

namespace Artificertech\FilamentMultiContext\Commands\Traits;

use Illuminate\Support\Str;

trait ContextAwareCommand
{
    protected function setContextPathAndNamespace(): void
    {
        $context = Str::of($this->option('context'))
            ->studly()
            ->trim('/')
            ->trim('\\')
            ->trim(' ');

        $resourcesPath = app_path($context) . '/Resources';
        $pagesPath = app_path($context) . '/Pages';
        $widgetsPath = app_path($context) . '/Widgets';
        $resourcesNamespace = $context
            ->replace('/', '\\')
            ->append('\\Resources')
            ->prepend('App\\');
        $pagesNamespace = $context
            ->replace('/', '\\')
            ->append('\\Pages')
            ->prepend('App\\');
        $widgetsNamespace = $context
            ->replace('/', '\\')
            ->append('\\Widgets')
            ->prepend('App\\');

        if (! blank($context)) {
            config([
                'filament.resources.path' => $resourcesPath,
                'filament.pages.path' => $pagesPath,
                'filament.widgets.path' => $widgetsPath,
                'filament.resources.namespace' => $resourcesNamespace,
                'filament.pages.namespace' => $pagesNamespace,
                'filament.widgets.namespace' => $widgetsNamespace,
            ]);
        }
    }
}
