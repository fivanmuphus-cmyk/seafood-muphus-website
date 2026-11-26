<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi menu')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama menu atau kelompok')
                            ->placeholder('Gurame')
                            ->helperText('Bisa satu hidangan (Kerapu Bakar) atau satu kelompok dengan banyak varian (Gurame).')
                            ->required()
                            ->maxLength(255),
                        Select::make('category')
                            ->label('Kategori')
                            ->options(fn () => config('muphus.menu_categories'))
                            ->required()
                            ->live(),
                        Select::make('subcategory')
                            ->label('Subkategori Seafood')
                            ->options(fn () => config('muphus.menu_subcategories'))
                            ->placeholder('Tanpa subkategori')
                            ->visible(fn ($get) => $get('category') === 'seafood'),
                        Textarea::make('description')
                            ->label('Deskripsi singkat')
                            ->helperText('Satu kalimat tentang rasa atau tekstur, maksimal 150 karakter. Boleh kosong.')
                            ->maxLength(150)
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Harga')
                    ->description('Isi harga tunggal, atau rentang (terendah dan tertinggi). Kosongkan keduanya bila harga ditampilkan per varian di bawah.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('price')
                            ->label('Harga (atau terendah)')
                            ->numeric()->minValue(0)->prefix('Rp'),
                        TextInput::make('price_max')
                            ->label('Harga tertinggi')
                            ->numeric()->minValue(0)->prefix('Rp'),
                        TextInput::make('price_note')
                            ->label('Satuan')
                            ->placeholder('ons')
                            ->helperText('Contoh: ons. Tampil sebagai "Rp 50.000 / ons".')
                            ->maxLength(30),
                    ]),

                Section::make('Varian dan harga')
                    ->description('Untuk kelompok seperti Gurame (bakar, goreng kremes, saus padang, dan seterusnya) atau isi paket. Harga per varian boleh kosong.')
                    ->collapsible()
                    ->schema([
                        Repeater::make('variants')
                            ->label('Daftar varian')
                            ->addActionLabel('Tambah varian')
                            ->reorderable()
                            ->collapsible()
                            ->defaultItems(0)
                            ->columns(4)
                            ->schema([
                                TextInput::make('name')->label('Nama')->required()->maxLength(120)->columnSpan(2),
                                TextInput::make('price')->label('Harga')->numeric()->minValue(0)->prefix('Rp'),
                                Toggle::make('spicy')->label('Pedas')->inline(false),
                                TextInput::make('note')->label('Catatan (opsional)')->maxLength(160)->columnSpanFull()
                                    ->helperText('Contoh: isi atau komposisi hidangan.'),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Foto dan tampilan')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Foto menu')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(11264)
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth('1600')
                            ->disk('public')
                            ->directory('menu')
                            ->visibility('public')
                            ->saveUploadedFileUsing(\App\Support\ImageOptimizer::saver('menu', 1200))
                            ->helperText('Tampil sebagai kartu di "Menu andalan" dan sebagai gambar kecil di "Daftar menu lengkap". Taruh hidangan di tengah. JPG, PNG, atau WebP, maksimal 11 MB (otomatis dikompres ke WebP).')
                            ->columnSpanFull(),
                        Select::make('badge')
                            ->label('Label')
                            ->options(['Terlaris' => 'Terlaris', 'Baru' => 'Baru'])
                            ->placeholder('Tanpa label'),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->helperText('Angka kecil tampil lebih dulu.')
                            ->numeric()->minValue(0)->default(0)->required(),
                        Toggle::make('is_active')->label('Aktif (tampil di website)')->default(true),
                        Toggle::make('show_as_card')->label('Tampil sebagai kartu di "Menu andalan"')->default(true),
                        Toggle::make('show_in_list')->label('Tampil di "Daftar menu lengkap"')->default(false),
                        Toggle::make('show_on_home')->label('Tampil di Beranda')->default(false),
                    ]),
            ]);
    }
}