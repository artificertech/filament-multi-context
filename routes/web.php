<?php

use Artificertech\FilamentMultiContext\Http\Middleware\ApplyContext;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

foreach (Filament::getContexts() as $context) {

    Route::domain($context::getDomain())
        ->middleware([ApplyContext::class . ':' . $context::class])
        ->group(function () use ($context) {
            Route::middleware($context::getBaseMiddleware())->group(function () use ($context) {
                Route::middleware($context::getAuthMiddleware())
                    ->name('filament.')
                    ->group(function () use ($context) {
                        Route::prefix($context::getPath())
                            ->middleware($context::getMiddlewares())
                            ->group($context->getRoutes());
                    });
            });
        });
}
