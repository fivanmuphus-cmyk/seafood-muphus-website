<?php

namespace App\Filament\Resources\SiteSettings\Tables;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteSettingsTable
{
    /** Alamat halaman di website untuk tiap kelompok pengaturan. */
    public const PATHS = [
        'general' => '/', 'home' => '/', 'about' => '/tentang', 'menu' => '/menu',
        'branches' => '/cabang', 'articles' => '/artikel', 'careers' => '/karir',
        'partnership' => '/kemitraan', 'contact' => '/kontak',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')->label('Halaman')
                    ->formatStateUsing(fn (string $state) => SiteSetting::GROUPS[$state] ?? $state)
                    ->weight('medium'),
                TextColumn::make('access')->label('Dapat diubah oleh')
                    ->state(fn (SiteSetting $record) => in_array($record->group, SiteSetting::ADMIN_GROUPS, true) ? 'Admin dan Superadmin' : 'Superadmin')
                    ->badge(),
                TextColumn::make('updated_at')->label('Terakhir diubah')->since()->placeholder('-'),
            ])
            ->recordActions([
                Action::make('lihat')
                    ->label('Lihat')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (SiteSetting $record) => url(self::PATHS[$record->group] ?? '/'))
                    ->openUrlInNewTab(),
                EditAction::make()->label('Ubah'),
            ])
            ->paginated(false);
    }
}