<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Resources\Pages\ListRecords;

class ListSiteSettings extends ListRecords
{
    protected static string $resource = SiteSettingResource::class;

    protected static ?string $title = 'Pengaturan Situs';

    public function mount(): void
    {
        SiteSetting::ensureRows();

        parent::mount();
    }
}