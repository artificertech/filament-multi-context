<?php

namespace Artificertech\FilamentMultiContext\Tests\App\FilamentTeams\Resources\UserResource\Pages;

use Artificertech\FilamentMultiContext\Tests\App\FilamentTeams\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
