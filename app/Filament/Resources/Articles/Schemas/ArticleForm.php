<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Artikel')->columns(2)->schema([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set, $get) {
                        // Alamat artikel diisi otomatis dari judul, hanya bila masih kosong.
                        if (blank($get('slug'))) {
                            $set('slug', Str::slug($state ?? ''));
                        }
                    })
                    ->columnSpanFull(),
                TextInput::make('slug')
                    ->label('Alamat artikel')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Terisi otomatis dari judul. Huruf kecil dan tanda hubung.'),
                Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'menu' => 'Menu',
                        'cerita' => 'Cerita Muphus',
                        'tips' => 'Tips',
                        'kabar' => 'Kabar',
                    ])
                    ->required(),
                Textarea::make('excerpt')
                    ->label('Ringkasan')
                    ->rows(3)
                    ->maxLength(300)
                    ->helperText('Tampil di kartu artikel dan hasil pencarian Google.')
                    ->columnSpanFull(),
                FileUpload::make('cover_path')
                    ->label('Foto utama')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(11264)
                    ->disk('public')
                    ->directory('articles')
                    ->visibility('public')
                    ->saveUploadedFileUsing(\App\Support\ImageOptimizer::saver('articles', 1600))
                    ->helperText('JPG, PNG, atau WebP, rasio 16:9, maksimal 11 MB (otomatis dikompres ke WebP).')
                    ->columnSpanFull(),
                MarkdownEditor::make('body')
                    ->label('Isi artikel')
                    ->required()
                    ->columnSpanFull(),
            ]),

            Section::make('Penerbitan')->columns(3)->schema([
                Select::make('status')
                    ->label('Status')
                    ->options(['draft' => 'Draf', 'published' => 'Terbit'])
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('published_at')
                    ->label('Tanggal terbit')
                    ->helperText('Kosongkan untuk memakai waktu saat disimpan.'),
                Toggle::make('is_featured')->label('Artikel unggulan'),
            ]),

            Section::make('SEO (opsional)')->columns(1)->collapsed()->schema([
                TextInput::make('seo_title')->label('Judul untuk Google')->maxLength(255),
                Textarea::make('seo_description')
                    ->label('Deskripsi untuk Google')
                    ->rows(2)
                    ->maxLength(300),
            ]),
        ]);
    }
}