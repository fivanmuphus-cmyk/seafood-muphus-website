<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi menu')->columns(2)->schema([
                ImageEntry::make('image_path')
                    ->label('Foto')
                    ->disk('public')
                    ->columnSpanFull(),
                TextEntry::make('name')
                    ->label('Nama menu'),
                TextEntry::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => config('muphus.menu_categories')[$state] ?? 'Lainnya'),
                TextEntry::make('price')
                    ->label('Harga')
                    ->money('IDR', locale: 'id', decimalPlaces: 0)
                    ->placeholder('Tidak ditampilkan'),
                TextEntry::make('badge')
                    ->label('Label')
                    ->badge()
                    ->placeholder('Tanpa label'),
                TextEntry::make('description')
                    ->label('Deskripsi')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]),
            Section::make('Tampilan di website')->columns(3)->schema([
                IconEntry::make('is_active')->label('Tampil di halaman Menu')->boolean(),
                IconEntry::make('show_on_home')->label('Tampil di Beranda')->boolean(),
                TextEntry::make('sort_order')->label('Urutan'),
            ]),
        ]);
    }
}