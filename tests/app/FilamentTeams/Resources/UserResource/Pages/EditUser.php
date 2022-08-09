<?php

namespace Artificertech\FilamentMultiContext\Tests\App\FilamentTeams\Resources\UserResource\Pages;

use Artificertech\FilamentMultiContext\Tests\App\FilamentTeams\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
