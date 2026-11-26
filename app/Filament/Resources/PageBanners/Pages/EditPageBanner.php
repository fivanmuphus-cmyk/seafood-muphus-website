<?php

namespace App\Filament\Resources\PageBanners\Pages;

use App\Filament\Resources\PageBanners\PageBannerResource;
use App\Models\PageBanner;
use Filament\Resources\Pages\EditRecord;

class EditPageBanner extends EditRecord
{
    protected static string $resource = PageBannerResource::class;

    public function getTitle(): string
    {
        return 'Banner ' . (PageBanner::PAGES[$this->record->page] ?? 'halaman');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Banner disimpan';
    }
}