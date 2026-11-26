<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Concerns\BackToList;
use App\Filament\Resources\Articles\ArticleResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    use BackToList;

    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lihat')
                ->label('Lihat di website')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn () => url($this->record->url()))
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->status === 'published'),
            DeleteAction::make(),
        ];
    }
}
