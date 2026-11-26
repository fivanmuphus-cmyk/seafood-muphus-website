<?php

namespace App\Filament\Resources\Branches\Schemas;

use App\Models\Branch;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi cabang')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama cabang')
                            ->placeholder('Seafood Muphus Babat')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('city')
                            ->label('Kota')
                            ->required()
                            ->maxLength(100)
                            ->datalist(fn (): array => Branch::query()
                                ->whereNotNull('city')
                                ->distinct()
                                ->orderBy('city')
                                ->pluck('city')
                                ->all()),
                        TextInput::make('address')
                            ->label('Alamat lengkap')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('whatsapp')
                            ->label('Nomor WhatsApp')
                            ->tel()
                            ->placeholder('0811xxxxxxx')
                            ->helperText('Boleh diawali 0 atau 62. Disimpan otomatis dalam format 62.')
                            ->regex('/^(\+?62|0)8[0-9]{8,12}$/')
                            ->validationMessages([
                                'regex' => 'Format nomor belum sesuai. Contoh: 0811xxxxxxx',
                            ])
                            ->dehydrateStateUsing(function (?string $state): ?string {
                                if (blank($state)) {
                                    return null;
                                }

                                $digits = preg_replace('/\D+/', '', $state);

                                if (str_starts_with($digits, '0')) {
                                    $digits = '62' . substr($digits, 1);
                                }

                                return $digits;
                            }),
                    ]),

                Section::make('Jam buka')
                    ->columns(2)
                    ->schema([
                        TimePicker::make('opens_at')
                            ->label('Jam buka')
                            ->seconds(false),
                        TimePicker::make('closes_at')
                            ->label('Jam tutup')
                            ->seconds(false),
                        TextInput::make('hours_note')
                            ->label('Catatan jam buka')
                            ->placeholder('Contoh: Libur pada hari raya')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Lokasi di peta')
                    ->description('Klik kanan lokasi di Google Maps untuk menyalin koordinatnya.')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90),
                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180),
                        TextInput::make('maps_url')
                            ->label('Tautan Google Maps')
                            ->url()
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),

                Section::make('Tampilan di website')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Tampilkan di website')
                            ->default(true),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->helperText('Angka kecil tampil lebih dulu.')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                    ]),
            ]);
    }
}