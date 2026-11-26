<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Filament\Concerns\BackToList;
use App\Filament\Resources\Branches\BranchResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBranch extends EditRecord
{
    use BackToList;

    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lihat')
                ->label('Lihat di website')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn () => url('/cabang'))
                ->openUrlInNewTab(),
            ViewAction::make()->label('Detail'),
            DeleteAction::make(),
        ];
    }
}
