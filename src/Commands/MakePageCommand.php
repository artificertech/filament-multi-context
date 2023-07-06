<?php

namespace Artificertech\FilamentMultiContext\Commands;

use Artificertech\FilamentMultiContext\Commands\Traits\ContextAwareCommand;
use Illuminate\Support\Str;
use ReflectionClass;

class MakePageCommand extends \Filament\Commands\MakePageCommand
{
    use ContextAwareCommand;

    protected $signature = 'make:filament-page {name?} {--R|resource=} {--T|type=} {--F|force} {--context=}';

    public function handle(): int
    {
        $this->setContextPathAndNamespace();

        return parent::handle();
    }

    protected function getDefaultStubPath(): string
    {
        $reflectionClass = new ReflectionClass(get_parent_class($this));

        return (string) Str::of($reflectionClass->getFileName())
            ->beforeLast('Commands')
            ->append('../stubs');
    }
}
