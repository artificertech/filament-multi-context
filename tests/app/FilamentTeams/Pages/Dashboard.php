<?php

namespace Artificertech\FilamentMultiContext\Tests\App\FilamentTeams\Pages;

use Artificertech\FilamentMultiContext\Concerns\ContextualPage;
use Filament\Pages\Dashboard as PagesDashboard;

class Dashboard extends PagesDashboard
{
    use ContextualPage;
}
