<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BranchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi cabang')->columns(2)->schema([
                TextEntry::make('name')->label('Nama cabang'),
                TextEntry::make('city')->label('Kota'),
                TextEntry::make('address')->label('Alamat')->columnSpanFull(),
                TextEntry::make('whatsapp')->label('WhatsApp')->placeholder('-'),
            ]),
            Section::make('Jam buka')->columns(3)->schema([
                TextEntry::make('opens_at')->label('Buka')->time('H:i')->placeholder('-'),
                TextEntry::make('closes_at')->label('Tutup')->time('H:i')->placeholder('-'),
                TextEntry::make('hours_note')->label('Catatan')->placeholder('-'),
            ]),
            Section::make('Lokasi di peta')->columns(3)->schema([
                TextEntry::make('latitude')->label('Latitude')->placeholder('-'),
                TextEntry::make('longitude')->label('Longitude')->placeholder('-'),
                TextEntry::make('maps_url')
                    ->label('Google Maps')
                    ->url(fn (?string $state): ?string => $state)
                    ->openUrlInNewTab()
                    ->placeholder('-'),
            ]),
            Section::make('Tampilan di website')->columns(2)->schema([
                IconEntry::make('is_active')->label('Tampil di website')->boolean(),
                TextEntry::make('sort_order')->label('Urutan'),
            ]),
        ]);
    }
}