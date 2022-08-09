<?php

namespace Artificertech\FilamentMultiContext;

use Filament\Facades\Filament;
use Filament\FilamentManager;
use Illuminate\Support\Traits\ForwardsCalls;

class FilamentMultiContextManager
{
    use ForwardsCalls;

    protected array $contexts = [];

    protected ?string $currentContext = null;

    public function __construct(FilamentManager $filament)
    {
        $this->contexts['filament'] = $filament;
    }

    public function setContext(string $context = null)
    {
        $this->currentContext = $context;

        return $this;
    }

    public function getContext()
    {
        return $this->contexts[$this->currentContext ?? 'filament'];
    }

    public function addContext(string $name)
    {
        $this->contexts[$name] = new FilamentManager;

        return $this;
    }

    public function forContext(string $context, callable $callback)
    {
        Filament::setContext($context);

        $callback();

        Filament::setContext();

        return $this;
    }

    public function forAllContexts(callable $callback)
    {
        foreach ($this->contexts as $key => $context) {
            Filament::setContext($context);

            $callback();

            Filament::setContext();
        }


        return $this;
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
        $response = $this->forwardCallTo($this->getContext(), $method, $parameters);

        if ($response instanceof FilamentManager) {
            return $this;
        }

        return $response;
    }
}
