<?php

namespace App\Filament\Resources\PageBanners\Schemas;

use App\Models\PageBanner;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class PageBannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // ---------- Hero Beranda (ukuran khusus) ----------
            Section::make('Hero Beranda')
                ->description('Foto besar di bagian paling atas Beranda. Gradien warna sudah tetap, yang bisa diganti hanya fotonya.')
                ->visible(fn (?Model $record) => $record?->page === 'beranda')
                ->schema([
                    TextInput::make('page')
                        ->label('Bagian')
                        ->formatStateUsing(fn (?string $state) => PageBanner::PAGES[$state] ?? $state)
                        ->disabled()
                        ->dehydrated(false),
                    FileUpload::make('image_path')
                        ->label('Foto hero')
                        ->image()
                        ->saveUploadedFileUsing(\App\Support\ImageOptimizer::saver('banners', 2400, 80))
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(10240)
                        ->disk('public')
                        ->directory('banners')
                        ->visibility('public')
                        ->imageEditor()
                        ->imageEditorAspectRatios(['24:11'])
                        ->imageCropAspectRatio('24:11')
                        ->imageResizeMode('cover')
                        ->imageResizeTargetWidth('2400')
                        ->imageResizeTargetHeight('1100')
                        ->helperText('Ukuran ideal 2400 × 1100 px (foto lebih kecil tetap bisa, tapi kurang tajam), JPG, PNG atau WebP, maksimal 10 MB (otomatis dikompres ke WebP). Taruh objek utama (hidangan atau suasana tenda) di SISI KANAN, karena teks berada di kiri. Kosongkan untuk hero tanpa foto.')
                        ->columnSpanFull(),
                ]),

            // ---------- Banner halaman lain (ukuran standar) ----------
            Section::make('Banner halaman')
                ->description('Gradien warna sudah tetap. Yang bisa diganti hanya foto dan teks.')
                ->visible(fn (?Model $record) => $record?->page !== 'beranda')
                ->columns(2)
                ->schema([
                    TextInput::make('page')
                        ->label('Halaman')
                        ->formatStateUsing(fn (?string $state) => PageBanner::PAGES[$state] ?? $state)
                        ->disabled()
                        ->dehydrated(false),
                    FileUpload::make('image_path')
                        ->label('Foto banner')
                        ->image()
                        ->saveUploadedFileUsing(\App\Support\ImageOptimizer::saver('banners', 2400, 80))
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(10240)
                        ->disk('public')
                        ->directory('banners')
                        ->visibility('public')
                        ->imageEditor()
                        ->imageEditorAspectRatios(['4:1'])
                        ->imageCropAspectRatio('4:1')
                        ->imageResizeMode('cover')
                        ->imageResizeTargetWidth('2400')
                        ->imageResizeTargetHeight('600')
                        ->helperText('Ukuran ideal 2400 × 600 px (rasio 4:1; foto lebih kecil tetap bisa, tapi kurang tajam), JPG, PNG atau WebP, maksimal 10 MB (otomatis dikompres ke WebP). Taruh objek penting di tengah bagian atas: bagian bawah memudar dan sisi kanan tertutup logo. Kosongkan untuk banner tanpa foto.')
                        ->columnSpanFull(),
                    TextInput::make('title')
                        ->label('Judul (opsional)')
                        ->helperText('Kosongkan untuk memakai judul bawaan halaman.')
                        ->maxLength(120),
                    TextInput::make('subtitle')
                        ->label('Subjudul (opsional)')
                        ->helperText('Kosongkan untuk memakai subjudul bawaan.')
                        ->maxLength(200),
                ]),
        ]);
    }
}