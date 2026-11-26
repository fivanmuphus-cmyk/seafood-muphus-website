<?php

namespace App\Filament\Resources\PageBanners;

use App\Filament\Resources\PageBanners\Pages\EditPageBanner;
use App\Filament\Resources\PageBanners\Pages\ListPageBanners;
use App\Filament\Resources\PageBanners\Schemas\PageBannerForm;
use App\Filament\Resources\PageBanners\Tables\PageBannersTable;
use App\Models\PageBanner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Satu banner per halaman, jadi admin hanya mengganti foto dan judulnya: tidak ada Tambah atau Hapus. */
class PageBannerResource extends Resource
{
    protected static ?string $model = PageBanner::class;

    protected static ?string $modelLabel = 'Banner halaman';

    protected static ?string $pluralModelLabel = 'Banner halaman';

    protected static ?string $navigationLabel = 'Banner halaman';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'page';

    public static function form(Schema $schema): Schema
    {
        return PageBannerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PageBannersTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPageBanners::route('/'),
            'edit' => EditPageBanner::route('/{record}/edit'),
        ];
    }
}