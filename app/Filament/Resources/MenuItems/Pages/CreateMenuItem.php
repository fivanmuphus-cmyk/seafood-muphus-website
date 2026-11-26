<?php

namespace App\Filament\Resources\MenuItems\Pages;

use App\Filament\Concerns\BackToList;
use App\Filament\Resources\MenuItems\MenuItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuItem extends CreateRecord
{
    use BackToList;

    protected static string $resource = MenuItemResource::class;
}
