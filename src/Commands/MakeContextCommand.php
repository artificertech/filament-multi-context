<?php

namespace Artificertech\FilamentMultiContext\Commands;

use Filament\Commands\Concerns;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeContextCommand extends Command
{
    use Concerns\CanManipulateFiles;
    use Concerns\CanValidateInput;

    protected $description = 'Creates a Filament Context class';

    protected $signature = 'make:filament-context {name?} {--F|force}';

    public function handle(): int
    {
        $context = (string) Str::of($this->argument('name') ?? $this->askRequired('Name (e.g. `AdminContext`)', 'name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $contextClass = (string) Str::of($context)->afterLast('\\');
        $contextNamespace = Str::of($context)->contains('\\') ?
            (string) Str::of($context)->beforeLast('\\') :
            '';

        $directoryPath = app_path(
            (string) Str::of($context)
                ->prepend('Filament\\')
                ->replace('\\', '/')
        );

        $path = (string) Str::of($directoryPath)
            ->append('.php');

        if (! $this->option('force') && $this->checkForCollision([
            $path,
        ])) {
            return static::INVALID;
        }

        $this->copyStubToApp('Context', $path, [
            'class' => $contextClass,
            'namespace' => 'App\\Filament' . ($contextNamespace !== '' ? "\\{$contextNamespace}" : ''),
        ]);

        app(Filesystem::class)->makeDirectory($directoryPath);
        app(Filesystem::class)->makeDirectory($directoryPath . '/Pages');
        app(Filesystem::class)->makeDirectory($directoryPath . '/Resources');
        app(Filesystem::class)->makeDirectory($directoryPath . '/Widgets');

        $this->info("Successfully created {$context}!");

        return static::SUCCESS;
    }

    protected function copyStubToApp(string $stub, string $targetPath, array $replacements = []): void
    {
        $filesystem = app(Filesystem::class);

        if (! $this->fileExists($stubPath = base_path("stubs/filament/{$stub}.stub"))) {
            $stubPath = __DIR__ . "/../../stubs/{$stub}.stub";
        }

        $stub = Str::of($filesystem->get($stubPath));

        foreach ($replacements as $key => $replacement) {
            $stub = $stub->replace("{{ {$key} }}", $replacement);
        }

        $stub = (string) $stub;

        $this->writeFile($targetPath, $stub);
    }
}
