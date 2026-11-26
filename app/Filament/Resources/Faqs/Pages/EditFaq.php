<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Concerns\BackToList;
use App\Filament\Resources\Faqs\FaqResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFaq extends EditRecord
{
    use BackToList;

    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
