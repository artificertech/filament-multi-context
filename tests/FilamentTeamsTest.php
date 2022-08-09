<?php

use Artificertech\FilamentMultiContext\Tests\App\FilamentTeams\Pages\Dashboard;
use Artificertech\FilamentMultiContext\Tests\App\FilamentTeams\Resources\UserResource;
use Artificertech\FilamentMultiContext\Tests\App\FilamentTeams\Resources\UserResource\RelationManagers\PostsRelationManager;
use Artificertech\FilamentMultiContext\Tests\App\Models\User;
use Filament\Facades\Filament;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('registers filament-teams pages', function () {
    Filament::forContext('filament-teams', function () {
        expect(Filament::getPages())->toContain(Dashboard::class);
    });

    actingAs(User::factory()->create());

    get(route('filament-teams.pages.dashboard'))
        ->assertSuccessful();
});

it('registers filament-teams resources', function () {
    Filament::forContext('filament-teams', function () {
        expect(Filament::getResources())->toContain(UserResource::class);
    });

    actingAs(User::factory()->create());

    get(route('filament-teams.resources.users.index'))
        ->assertSuccessful();
});

it('registers filament-teams relation managers', function () {
    actingAs($user = User::factory()->hasPosts(3)->create());

    get(route('filament-teams.resources.users.edit', ['record' => $user]))
        ->assertSuccessful()
        ->assertSeeLivewire(PostsRelationManager::class);
});
