<?php

namespace Artificertech\FilamentMultiContext\Http\Middleware;

use Closure;
use Filament\Facades\Filament;

class ApplyContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next, $context)
    {
        Filament::setContext($context);

        return $next($request);
    }
}
