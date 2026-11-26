<?php

namespace App\Filament\Resources\Branches\Tables;

use App\Models\Branch;
use App\Filament\Resources\Branches\BranchResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama cabang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city')
                    ->label('Kota')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('peta')
                    ->label('Peta')
                    ->state(fn (Branch $record): bool => ($record->latitude && $record->longitude) || filled($record->maps_url))
                    ->boolean()
                    ->trueIcon('heroicon-o-map-pin')
                    ->falseIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn (Branch $record): string => ($record->latitude && $record->longitude) || filled($record->maps_url)
                        ? 'Lokasi di peta sudah diisi'
                        : 'Lokasi di peta belum diisi, cabang belum muncul di peta'),
                TextColumn::make('opens_at')
                    ->label('Buka')
                    ->time('H:i'),
                TextColumn::make('closes_at')
                    ->label('Tutup')
                    ->time('H:i'),
                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->toggleable(),
                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_active')
                    ->label('Tampil'),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('city')
                    ->label('Kota')
                    ->options(fn (): array => Branch::query()
                        ->orderBy('city')
                        ->distinct()
                        ->pluck('city', 'city')
                        ->all()),
                TernaryFilter::make('is_active')
                    ->label('Status tampil'),
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
            ->emptyStateIcon('heroicon-o-map-pin')
            ->emptyStateHeading('Belum ada cabang')
            ->emptyStateDescription('Tambahkan cabang pertama agar tampil di halaman Cabang dan di peta.')
            ->emptyStateActions([
                Action::make('buat')
                    ->label('Tambah cabang')
                    ->icon('heroicon-m-plus')
                    ->url(BranchResource::getUrl('create')),
            ]);
    }
}