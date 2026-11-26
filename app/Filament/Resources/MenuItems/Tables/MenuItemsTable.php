<?php

namespace App\Filament\Resources\MenuItems\Tables;

use App\Filament\Resources\MenuItems\MenuItemResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MenuItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')->label('Foto')->disk('public')->square(),
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('category')->label('Kategori')
                    ->formatStateUsing(fn (?string $state) => config('muphus.menu_categories')[$state] ?? $state)
                    ->badge()->sortable(),
                TextColumn::make('subcategory')->label('Subkategori')
                    ->formatStateUsing(fn (?string $state) => config('muphus.menu_subcategories')[$state] ?? $state)
                    ->placeholder('-')->toggleable(),
                TextColumn::make('variants')->label('Varian')
                    ->state(fn ($record) => count($record->variants ?? []) ?: null)
                    ->placeholder('-')->toggleable(),
                TextColumn::make('price')->label('Harga')
                    ->state(fn ($record) => $record->priceLabel())
                    ->placeholder('-'),
                ToggleColumn::make('show_as_card')->label('Andalan'),
                ToggleColumn::make('show_in_list')->label('Daftar'),
                ToggleColumn::make('show_on_home')->label('Beranda'),
                ToggleColumn::make('is_active')->label('Aktif'),
                TextColumn::make('sort_order')->label('Urutan')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->paginationPageOptions([25, 50, 100])
            ->filters([
                SelectFilter::make('category')->label('Kategori')->options(fn () => config('muphus.menu_categories')),
                SelectFilter::make('subcategory')->label('Subkategori')->options(fn () => config('muphus.menu_subcategories')),
                TernaryFilter::make('is_active')->label('Aktif'),
            ])
            ->recordActions([
                ViewAction::make()->label('Detail'),
                EditAction::make()->label('Ubah'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-cake')
            ->emptyStateHeading('Belum ada menu')
            ->emptyStateDescription('Tambahkan menu pertama agar tampil di halaman Menu.')
            ->emptyStateActions([
                Action::make('buat')
                    ->label('Tambah menu')
                    ->icon('heroicon-m-plus')
                    ->url(MenuItemResource::getUrl('create')),
            ]);
    }
}