<?php

namespace Artificertech\FilamentMultiContext\Commands;

use Artificertech\FilamentMultiContext\Commands\Traits\ContextAwareCommand;
use Illuminate\Support\Str;
use ReflectionClass;

class MakeRelationManagerCommand extends \Filament\Commands\MakeRelationManagerCommand
{
    use ContextAwareCommand;

    protected $signature = 'make:filament-relation-manager {resource?} {relationship?} {recordTitleAttribute?} {--attach} {--associate} {--soft-deletes} {--view} {--F|force} {--context=}';

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
