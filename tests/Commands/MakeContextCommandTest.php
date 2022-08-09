<?php

use Illuminate\Filesystem\Filesystem;

afterEach(function () {
    app(Filesystem::class)->deleteDirectory(app_path('FilamentTeams'));
    app(Filesystem::class)->delete(app_path('Providers/FilamentTeamsServiceProvider.php'));
    app(Filesystem::class)->delete(config_path('filament-teams.php'));
});

it('creates a context', function () {
    $this->artisan('make:filament-context')
        ->expectsQuestion('Name (e.g. `FilamentTeams`)', 'FilamentTeams')
        ->assertExitCode(0);
});
