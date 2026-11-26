<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pengirim')->columns(3)->schema([
                TextEntry::make('name')->label('Nama'),
                TextEntry::make('email')->label('Email')->copyable()->placeholder('-'),
                TextEntry::make('phone')->label('WhatsApp')->copyable()->placeholder('-'),
                TextEntry::make('topic')->label('Topik')->badge()
                    ->formatStateUsing(fn (?string $state) => config('muphus.contact_topics')[$state] ?? $state),
                TextEntry::make('created_at')->label('Masuk')->dateTime('d M Y, H:i'),
                TextEntry::make('read_at')->label('Dibaca')->dateTime('d M Y, H:i')->placeholder('Belum'),
            ]),
            Section::make('Pesan')->schema([
                TextEntry::make('message')->hiddenLabel()->prose()->columnSpanFull(),
            ]),
        ]);
    }
}