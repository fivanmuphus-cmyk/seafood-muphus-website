<?php

namespace App\Filament\Resources\Faqs\Tables;

use App\Filament\Resources\Faqs\FaqResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FaqsTable
{
    private const PAGES = [
        'beranda' => 'Beranda', 'tentang' => 'Tentang', 'menu' => 'Menu',
        'cabang' => 'Cabang', 'karir' => 'Karir', 'kontak' => 'Kontak',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('page')->label('Halaman')->badge()->sortable()
                    ->formatStateUsing(fn (string $state) => self::PAGES[$state] ?? $state),
                TextColumn::make('question')->label('Pertanyaan')->searchable()->limit(60),
                TextColumn::make('sort_order')->label('Urutan')->sortable(),
                ToggleColumn::make('is_active')->label('Tampil'),
            ])
            // Dikelompokkan per halaman, lalu menurut urutan.
            ->defaultSort(fn ($query) => $query->orderBy('page')->orderBy('sort_order'))
            ->filters([
                SelectFilter::make('page')->label('Halaman')->options(self::PAGES),
            ])
            ->recordActions([EditAction::make()->label('Ubah')])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->emptyStateIcon('heroicon-o-question-mark-circle')
            ->emptyStateHeading('Belum ada FAQ')
            ->emptyStateDescription('Tambahkan pertanyaan yang sering ditanyakan pelanggan.')
            ->emptyStateActions([
                Action::make('buat')
                    ->label('Tambah FAQ')
                    ->icon('heroicon-m-plus')
                    ->url(FaqResource::getUrl('create')),
            ]);
    }
}
