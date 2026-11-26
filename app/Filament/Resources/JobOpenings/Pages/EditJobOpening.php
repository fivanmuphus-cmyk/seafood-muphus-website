<?php

namespace App\Filament\Resources\JobOpenings\Pages;

use App\Filament\Concerns\BackToList;
use App\Filament\Resources\JobOpenings\JobOpeningResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJobOpening extends EditRecord
{
    use BackToList;

    protected static string $resource = JobOpeningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lihat')
                ->label('Lihat di website')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn () => url('/karir'))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
