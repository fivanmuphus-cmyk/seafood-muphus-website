<?php

namespace App\Filament\Resources\MenuItems\Pages;

use App\Filament\Resources\MenuItems\MenuItemResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMenuItem extends ViewRecord
{
    protected static string $resource = MenuItemResource::class;

    public function getTitle(): string
        {
            return 'Detail: ' . $this->record->name;
        }
    public function getBreadcrumb(): string
    {
        return 'Detail';
    }
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}


