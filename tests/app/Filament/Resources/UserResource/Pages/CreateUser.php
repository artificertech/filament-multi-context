<?php

namespace Artificertech\FilamentMultiContext\Tests\App\Filament\Resources\UserResource\Pages;

use Artificertech\FilamentMultiContext\Tests\App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
