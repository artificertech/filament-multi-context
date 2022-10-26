<?php

use Artificertech\FilamentMultiContext\Tests\App\Filament\Pages\Dashboard;
use Artificertech\FilamentMultiContext\Tests\App\Filament\Resources\UserResource;
use Artificertech\FilamentMultiContext\Tests\App\Filament\Resources\UserResource\RelationManagers\PostsRelationManager;
use Artificertech\FilamentMultiContext\Tests\App\Models\User;
use Filament\Facades\Filament;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('registers filament pages', function () {
    expect(Filament::getPages())->toContain(Dashboard::class);

    Filament::forContext('filament', function () {
        expect(Filament::getPages())->toContain(Dashboard::class);
    });

    actingAs(User::factory()->create());

    get(route('filament.pages.dashboard'))
        ->assertSuccessful();
});

it('registers filament resources', function () {
    expect(Filament::getResources())->toContain(UserResource::class);

    Filament::forContext('filament', function () {
        expect(Filament::getResources())->toContain(UserResource::class);
    });

    actingAs(User::factory()->create());

    get(route('filament.resources.users.index'))
        ->assertSuccessful();
});

it('registers filament relation managers', function () {
    actingAs($user = User::factory()->hasPosts(3)->create());

    get(route('filament.resources.users.edit', ['record' => $user]))
        ->assertSuccessful()
        ->assertSeeLivewire(PostsRelationManager::class);
});

it('returns filament configured guard', function () {
    $guardName = config('filament.auth.guard');

    $reflectionClass = new ReflectionClass($guard = Filament::auth());

    expect($reflectionClass->getProperty('name')->getValue($guard))->toBe($guardName);
});
