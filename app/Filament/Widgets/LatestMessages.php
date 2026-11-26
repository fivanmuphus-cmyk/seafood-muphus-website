<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestMessages extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Pesan terbaru')
            ->query(ContactMessage::query()->latest()->limit(5))
            ->headerActions([
                Action::make('all')->label('Lihat semua')->link()
                    ->url(ContactMessageResource::getUrl('index')),
            ])
            ->columns([
                TextColumn::make('name')->label('Nama')->weight('medium'),
                TextColumn::make('topic')->label('Topik')->badge()
                    ->formatStateUsing(fn (?string $state) => config('muphus.contact_topics')[$state] ?? $state),
                TextColumn::make('message')->label('Pesan')->limit(60)->wrap(),
                TextColumn::make('read_at')->label('Status')->badge()
                    ->state(fn (ContactMessage $record) => $record->read_at ? 'Dibaca' : 'Baru')
                    ->color(fn (string $state) => $state === 'Baru' ? 'danger' : 'gray'),
                TextColumn::make('created_at')->label('Masuk')->since(),
            ])
            ->recordUrl(fn (ContactMessage $record) => ContactMessageResource::getUrl('view', ['record' => $record]))
            ->recordClasses(fn (ContactMessage $record) => $record->read_at === null ? 'muphus-unread' : null)
            ->emptyStateHeading('Belum ada pesan masuk')
            ->emptyStateDescription('Pesan dari formulir Kontak akan muncul di sini.')
            ->emptyStateIcon('heroicon-o-envelope')
            ->paginated(false);
    }
}