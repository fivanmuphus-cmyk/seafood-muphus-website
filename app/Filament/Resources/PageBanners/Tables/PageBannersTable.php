<?php

namespace App\Filament\Resources\PageBanners\Tables;

use App\Models\PageBanner;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageBannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('page')
                    ->label('Halaman')
                    ->weight('medium')
                    ->formatStateUsing(fn (?string $state) => PageBanner::PAGES[$state] ?? $state),
                ImageColumn::make('image_path')
                    ->label('Foto')
                    ->disk('public')
                    ->width(160)
                    ->height(48)
                    ->defaultImageUrl(null),
                TextColumn::make('title')->label('Judul')->placeholder('(bawaan)'),
                TextColumn::make('updated_at')->label('Diperbarui')->since()->placeholder('-'),
            ])
            ->recordUrl(fn (PageBanner $record) => \App\Filament\Resources\PageBanners\PageBannerResource::getUrl('edit', ['record' => $record]))
            ->recordActions([EditAction::make()->label('Ubah')])
            ->paginated(false);
    }
}