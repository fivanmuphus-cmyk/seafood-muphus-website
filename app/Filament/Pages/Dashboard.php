<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\IncompleteData;
use App\Filament\Widgets\LatestMessages;
use App\Filament\Widgets\QuickActions;
use App\Filament\Widgets\SiteOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Ringkasan';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $title = 'Ringkasan';

    public function getWidgets(): array
    {
        return [SiteOverview::class, QuickActions::class, IncompleteData::class, LatestMessages::class];
    }

    public function getColumns(): int|array
    {
        return ['default' => 1, 'lg' => 2];
    }
}