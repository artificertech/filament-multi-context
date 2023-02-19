<?php

use Artificertech\FilamentMultiContext\Tests\App\FilamentTeams\Pages\Dashboard;
use Artificertech\FilamentMultiContext\Tests\App\FilamentTeams\Resources\UserResource;
use Artificertech\FilamentMultiContext\Tests\App\FilamentTeams\Resources\UserResource\Pages\CreateUser;
use Artificertech\FilamentMultiContext\Tests\App\FilamentTeams\Resources\UserResource\RelationManagers\PostsRelationManager;
use Artificertech\FilamentMultiContext\Tests\App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
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

    get(route('filament-teams.resources.teams-users.index'))
        ->assertSuccessful();
});

it('registers filament-teams livewire component aliases', function () {
    Filament::forContext('filament-teams', function () {
        $createUserAliases = array_keys(Livewire::getComponentAliases(), CreateUser::class);

        expect($createUserAliases)->toContain('filament-teams.resources.user-resource.pages.create-user');
    });
});

it('registers filament-teams relation managers', function () {
    actingAs($user = User::factory()->hasPosts(3)->create());

    get(route('filament-teams.resources.teams-users.edit', ['record' => $user]))
        ->assertSuccessful()
        ->assertSeeLivewire(PostsRelationManager::class);
});

it('registers filament-teams render hooks', function () {
    Filament::forContext('filament-teams', function () {
        event(new \Filament\Events\ServingFilament());
        expect(Filament::renderHook('test'))->toHtml()->toBe('something');
    });
});

it('returns filament guard if none is defined', function () {
    $guardName = config('filament.auth.guard');

    Filament::setContext('filament-teams');

    $reflectionClass = new ReflectionClass($guard = Filament::auth());
    $property = $reflectionClass->getProperty('name');
    $property->setAccessible(true);
    expect($property->getValue($guard))->toBe($guardName);
});

it('returns guard if one is defined', function () {
    $this->app['config']->set('auth.guards.web2', [
        'driver' => 'session',
        'provider' => 'users',
    ]);
    $this->app['config']->set('filament-teams.auth.guard', 'web2');

    Filament::setContext('filament-teams');

    $reflectionClass = new ReflectionClass($guard = Filament::auth());
    $property = $reflectionClass->getProperty('name');
    $property->setAccessible(true);
    expect($property->getValue($guard))->toBe('web2');
});
