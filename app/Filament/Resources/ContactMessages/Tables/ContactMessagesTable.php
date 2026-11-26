<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->weight('medium'),
                TextColumn::make('topic')->label('Topik')->badge()
                    ->formatStateUsing(fn (?string $state) => config('muphus.contact_topics')[$state] ?? $state),
                TextColumn::make('message')->label('Pesan')->limit(70)->wrap()->searchable(),
                TextColumn::make('phone')->label('WhatsApp')->placeholder('-')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')->label('Email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Masuk')->since()->sortable()
                    ->tooltip(fn (ContactMessage $record) => $record->created_at?->format('d M Y, H:i')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('topic')->label('Topik')->options(config('muphus.contact_topics')),
            ])
            ->recordUrl(fn (ContactMessage $record) => ContactMessageResource::getUrl('view', ['record' => $record]))
            ->recordClasses(fn (ContactMessage $record) => $record->read_at === null ? 'muphus-unread' : null)
            ->recordActions([
                Action::make('markRead')
                    ->label('Tandai dibaca')
                    ->icon('heroicon-o-check')
                    ->color('gray')
                    ->visible(fn (ContactMessage $record) => $record->read_at === null)
                    ->action(fn (ContactMessage $record) => $record->update(['read_at' => now()])),
                ViewAction::make()->label('Buka'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markRead')
                        ->label('Tandai dibaca')
                        ->icon('heroicon-o-check')
                        ->action(fn (Collection $records) => $records->each->update(['read_at' => now()]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada pesan masuk')
            ->emptyStateDescription('Pesan dari formulir Kontak di situs akan muncul di sini.')
            ->emptyStateIcon('heroicon-o-envelope')
            ->paginated([10, 25, 50]);
    }
}