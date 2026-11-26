<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;

    protected static ?string $title = 'Pesan masuk';

    /** Tanpa tombol di kepala halaman: pesan datang dari formulir situs. */
    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua'),
            'baru' => Tab::make('Belum dibaca')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('read_at'))
                ->badge(ContactMessage::query()->whereNull('read_at')->count() ?: null)
                ->badgeColor('danger'),
        ];
    }
}