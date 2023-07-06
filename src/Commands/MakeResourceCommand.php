<?php

namespace Artificertech\FilamentMultiContext\Commands;

use Artificertech\FilamentMultiContext\Commands\Traits\ContextAwareCommand;
use Illuminate\Support\Str;
use ReflectionClass;

class MakeResourceCommand extends \Filament\Commands\MakeResourceCommand
{
    use ContextAwareCommand;

    protected $signature = 'make:filament-resource {name?} {--soft-deletes} {--view} {--G|generate} {--S|simple} {--F|force} {--context=}';

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
