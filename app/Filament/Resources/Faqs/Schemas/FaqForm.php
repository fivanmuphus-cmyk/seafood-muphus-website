<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pertanyaan dan jawaban')->columns(2)->schema([
                Select::make('page')
                    ->label('Tampil di halaman')
                    ->options([
                        'beranda' => 'Beranda', 'tentang' => 'Tentang', 'menu' => 'Menu',
                        'cabang' => 'Cabang', 'karir' => 'Karir', 'kontak' => 'Kontak',
                    ])
                    ->required(),
                TextInput::make('sort_order')
                    ->label('Urutan')->numeric()->minValue(0)->default(0)->required()
                    ->helperText('Angka kecil tampil lebih dulu.'),
                TextInput::make('question')
                    ->label('Pertanyaan')->required()->maxLength(255)->columnSpanFull(),
                Textarea::make('answer')
                    ->label('Jawaban')->required()->rows(4)->maxLength(600)
                    ->helperText('Singkat, maksimal 2 sampai 3 kalimat.')->columnSpanFull(),
                Toggle::make('is_active')->label('Tampilkan')->default(true),
            ]),
        ]);
    }
}