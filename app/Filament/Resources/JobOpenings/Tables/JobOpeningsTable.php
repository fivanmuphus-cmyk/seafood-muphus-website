<?php

namespace App\Filament\Resources\JobOpenings\Tables;

use App\Filament\Resources\JobOpenings\JobOpeningResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JobOpeningsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('brochure_path')->label('Brosur')->disk('public'),
                TextColumn::make('type')->label('Jenis')->badge()->sortable()
                    ->formatStateUsing(fn (string $state) => $state === 'office' ? 'Office' : 'Outlet'),
                TextColumn::make('position')->label('Posisi')->searchable()->sortable(),
                TextColumn::make('city')->label('Kota')->placeholder('-')->toggleable(),
                TextColumn::make('division')->label('Divisi')->placeholder('-')->toggleable(),
                // Merah bila batas pendaftaran sudah lewat (lowongan otomatis tersembunyi di website).
                TextColumn::make('deadline')->label('Batas')->date('d M Y')->sortable()
                    ->placeholder('Tanpa batas')
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : null)
                    ->description(fn ($state) => $state && $state->isPast() ? 'Sudah lewat' : null),
                ToggleColumn::make('is_active')->label('Tampil'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('type')->label('Jenis')
                    ->options(['outlet' => 'Outlet', 'office' => 'Office']),
            ])
            ->recordActions([EditAction::make()->label('Ubah')])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->emptyStateIcon('heroicon-o-briefcase')
            ->emptyStateHeading('Belum ada lowongan')
            ->emptyStateDescription('Tambahkan lowongan agar tampil di halaman Karir.')
            ->emptyStateActions([
                Action::make('buat')
                    ->label('Tambah lowongan')
                    ->icon('heroicon-m-plus')
                    ->url(JobOpeningResource::getUrl('create')),
            ]);
    }
}
