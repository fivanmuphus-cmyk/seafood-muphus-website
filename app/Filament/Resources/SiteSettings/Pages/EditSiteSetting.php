<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Filament\Resources\SiteSettings\Tables\SiteSettingsTable;
use App\Models\SiteSetting;
use App\Support\Site;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditSiteSetting extends EditRecord
{
    protected static string $resource = SiteSettingResource::class;

    public function getTitle(): string
    {
        return 'Ubah ' . (SiteSetting::GROUPS[$this->record->group] ?? 'pengaturan');
    }

    /** Kolom yang kosong ditampilkan dengan teks bawaan, supaya admin tahu apa yang sedang tayang. */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['data'] = Site::withDefaults($this->record->group, $data['data'] ?? []);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lihat')
                ->label('Lihat di website')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn () => url(SiteSettingsTable::PATHS[$this->record->group] ?? '/'))
                ->openUrlInNewTab(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Pengaturan berhasil disimpan. Perubahan langsung tampil di website.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}