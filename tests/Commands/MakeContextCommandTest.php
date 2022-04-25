<?php

use Illuminate\Filesystem\Filesystem;

afterEach(function () {
    app(Filesystem::class)->deleteDirectory(app_path('Filament'));
});

it('creates context class', function () {
    $this->artisan('make:filament-context')
        ->expectsQuestion('Name (e.g. `AdminContext`)', 'AdminContext')
        ->assertExitCode(0);
});
