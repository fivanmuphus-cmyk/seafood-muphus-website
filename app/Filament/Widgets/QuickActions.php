<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Branches\BranchResource;
use App\Filament\Resources\JobOpenings\JobOpeningResource;
use App\Filament\Resources\MenuItems\MenuItemResource;
use App\Filament\Resources\SiteSettings\SiteSettingResource;
use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected static ?int $sort = 2;

    protected string $view = 'filament.widgets.quick-actions';

    protected int|string|array $columnSpan = 1;

    protected function getViewData(): array
    {
        return [
            'links' => [
                ['Tulis artikel', ArticleResource::getUrl('create'), 'heroicon-o-newspaper'],
                ['Tambah cabang', BranchResource::getUrl('create'), 'heroicon-o-map-pin'],
                ['Tambah menu', MenuItemResource::getUrl('create'), 'heroicon-o-rectangle-stack'],
                ['Pasang lowongan', JobOpeningResource::getUrl('create'), 'heroicon-o-briefcase'],
                ['Ubah teks situs', SiteSettingResource::getUrl('index'), 'heroicon-o-pencil-square'],
            ],
        ];
    }
}