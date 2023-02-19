<?php

namespace Artificertech\FilamentMultiContext\Commands;

use Filament\Support\Commands\Concerns;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeContextCommand extends Command
{
    use Concerns\CanManipulateFiles;
    use Concerns\CanValidateInput;

    protected $description = 'Create a Filament Context';

    protected $signature = 'make:filament-context {name?} {--F|force}';

    public function handle(): int
    {
        $context = Str::of($this->getContextInput())
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $this->copyStubs($context);

        $this->createDirectories($context);

        $this->info("Successfully created {$context} context!");

        return static::SUCCESS;
    }

    public function getContextInput(): string
    {
        return $this->validateInput(
            fn () => $this->argument('name') ?? $this->askRequired('Name (e.g. `FilamentTeams`)', 'name'),
            'name',
            ['required', 'not_in:filament']
        );
    }

    protected function copyStubs($context)
    {
        $serviceProviderClass = $context->afterLast('\\')->append('ServiceProvider');

        $contextName = $context->afterLast('\\')->kebab();

        $serviceProviderPath = $serviceProviderClass
            ->prepend('/')
            ->prepend(app_path('Providers'))
            ->append('.php');

        $configPath = config_path($contextName->prepend('/')->append('.php'));

        $contextNamespace = $context
            ->replace('\\', '\\\\')
            ->prepend('\\\\')
            ->prepend('App');

        if (! $this->option('force') && $this->checkForCollision([
            $serviceProviderPath,
        ])) {
            return static::INVALID;
        }

        if (! $this->option('force') && $this->checkForCollision([
            $configPath,
        ])) {
            return static::INVALID;
        }

        $this->copyStubToApp('ContextServiceProvider', $serviceProviderPath, [
            'class' => (string) $serviceProviderClass,
            'name' => (string) $contextName,
        ]);

        $this->copyStubToApp('config', $configPath, [
            'namespace' => (string) $contextNamespace,
            'path' => (string) $context->replace('\\', '/'),
        ]);
    }

    protected function createDirectories($context)
    {
        $directoryPath = app_path(
            (string) $context
                ->replace('\\', '/')
        );

        app(Filesystem::class)->makeDirectory($directoryPath, force: $this->option('force'));
        app(Filesystem::class)->makeDirectory($directoryPath.'/Pages', force: $this->option('force'));
        app(Filesystem::class)->makeDirectory($directoryPath.'/Resources', force: $this->option('force'));
        app(Filesystem::class)->makeDirectory($directoryPath.'/Widgets', force: $this->option('force'));
    }

    protected function copyStubToApp(string $stub, string $targetPath, array $replacements = []): void
    {
        $filesystem = app(Filesystem::class);

        if (! $this->fileExists($stubPath = base_path("stubs/filament/{$stub}.stub"))) {
            $stubPath = __DIR__."/../../stubs/{$stub}.stub";
        }

        $stub = Str::of($filesystem->get($stubPath));

        foreach ($replacements as $key => $replacement) {
            $stub = $stub->replace("{{ {$key} }}", $replacement);
        }

        $stub = (string) $stub;

        $this->writeFile($targetPath, $stub);
    }
}
