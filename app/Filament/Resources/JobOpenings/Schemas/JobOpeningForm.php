<?php

namespace App\Filament\Resources\JobOpenings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobOpeningForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Lowongan')->columns(2)->schema([
                Select::make('type')
                    ->label('Jenis')
                    ->options(['outlet' => 'Outlet', 'office' => 'Office'])
                    ->required()->live(),
                TextInput::make('position')
                    ->label('Nama posisi')->required()->maxLength(255),
                TextInput::make('city')
                    ->label('Kota / penempatan')->maxLength(100)
                    ->visible(fn ($get) => $get('type') !== 'office')
                    ->helperText('Contoh: Salatiga'),
                TextInput::make('division')
                    ->label('Divisi')->maxLength(100)
                    ->visible(fn ($get) => $get('type') === 'office')
                    ->helperText('Contoh: Finance'),
                TextInput::make('hook')
                    ->label('Kalimat ajakan')->maxLength(160)->columnSpanFull()
                    ->helperText('Satu kalimat, maksimal 12 kata.'),
                Textarea::make('summary')
                    ->label('Ringkasan')->rows(3)->maxLength(400)->columnSpanFull(),
            ]),
            Section::make('Pendaftaran')->columns(2)->schema([
                TextInput::make('form_url')
                    ->label('Tautan Google Form')
                    ->url()->required()->maxLength(500)
                    ->rule('regex:/^https:\/\//')
                    ->validationMessages(['regex' => 'Tautan harus diawali https://'])
                    ->helperText('Hanya tautan https. Pastikan formulirnya milik Muphus.')
                    ->columnSpanFull(),
                DatePicker::make('deadline')
                    ->label('Batas pendaftaran')
                    ->helperText('Kosongkan bila tidak ada. Setelah lewat, lowongan otomatis tersembunyi.'),
                FileUpload::make('brochure_path')
                    ->saveUploadedFileUsing(\App\Support\ImageOptimizer::saver('jobs', 1500, 85))
                    ->label('Brosur')->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(10240)
                    ->imageResizeMode('contain')
                    ->imageResizeTargetWidth('1600')
                    ->disk('public')->directory('jobs')->visibility('public')
                    ->helperText('JPG, PNG, atau WebP. Rasio 3:4, maksimal 10 MB (otomatis dikompres ke WebP).'),
            ]),
            Section::make('Tampilan di website')->columns(2)->schema([
                Toggle::make('is_active')->label('Tampilkan')->default(true),
                TextInput::make('sort_order')
                    ->label('Urutan')->numeric()->minValue(0)->default(0)->required(),
            ]),
        ]);
    }
}