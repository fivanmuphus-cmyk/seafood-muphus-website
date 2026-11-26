<?php

namespace App\Filament\Resources\Articles\Tables;

use App\Filament\Resources\Articles\ArticleResource;
use App\Models\Article;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_path')->label('Foto')->disk('public'),
                TextColumn::make('title')->label('Judul')->searchable()->limit(50),
                TextColumn::make('category')->label('Kategori')->badge()
                    ->formatStateUsing(fn (string $state, Article $record) => $record->categoryLabel()),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'published' ? 'Terbit' : 'Draf')
                    ->color(fn (string $state) => $state === 'published' ? 'success' : 'gray'),
                IconColumn::make('is_featured')->label('Unggulan')->boolean()
                    ->trueIcon('heroicon-s-star')->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')->falseColor('gray'),
                TextColumn::make('published_at')->label('Terbit')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('status')->label('Status')
                    ->options(['draft' => 'Draf', 'published' => 'Terbit']),
                SelectFilter::make('category')->label('Kategori')->options([
                    'menu' => 'Menu', 'cerita' => 'Cerita Muphus', 'tips' => 'Tips', 'kabar' => 'Kabar',
                ]),
            ])
            ->recordActions([
                Action::make('lihat')
                    ->label('Lihat')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Article $record) => url($record->url()))
                    ->openUrlInNewTab()
                    ->visible(fn (Article $record) => $record->status === 'published'),
                EditAction::make()->label('Ubah'),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->emptyStateIcon('heroicon-o-newspaper')
            ->emptyStateHeading('Belum ada artikel')
            ->emptyStateDescription('Tulis artikel pertama untuk tampil di halaman Artikel.')
            ->emptyStateActions([
                Action::make('buat')
                    ->label('Tulis artikel')
                    ->icon('heroicon-m-plus')
                    ->url(ArticleResource::getUrl('create')),
            ]);
    }
}
