<?php

namespace App\Filament\Resources\MenuItems\Pages;

use App\Filament\Concerns\BackToList;
use App\Filament\Resources\MenuItems\MenuItemResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMenuItem extends EditRecord
{
    use BackToList;

    protected static string $resource = MenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lihat')
                ->label('Lihat di website')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn () => url('/menu'))
                ->openUrlInNewTab(),
            ViewAction::make()->label('Detail'),
            DeleteAction::make(),
        ];
    }
}
