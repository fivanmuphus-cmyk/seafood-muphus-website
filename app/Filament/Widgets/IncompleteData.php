<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Branches\BranchResource;
use App\Filament\Resources\JobOpenings\JobOpeningResource;
use App\Filament\Resources\MenuItems\MenuItemResource;
use App\Models\Article;
use App\Models\Branch;
use App\Models\JobOpening;
use App\Models\MenuItem;
use Filament\Widgets\Widget;

class IncompleteData extends Widget
{
    protected static ?int $sort = 3;

    protected string $view = 'filament.widgets.incomplete-data';

    protected int|string|array $columnSpan = 1;

    protected function getViewData(): array
    {
        $items = [
            ['Cabang aktif belum punya peta', Branch::query()->active()
                ->whereNull('latitude')->whereNull('maps_url')->count(), BranchResource::getUrl('index')],
            ['Cabang aktif belum punya jam buka', Branch::query()->active()
                ->where(fn ($q) => $q->whereNull('opens_at')->orWhereNull('closes_at'))->count(), BranchResource::getUrl('index')],
            ['Cabang aktif belum punya nomor WhatsApp', Branch::query()->active()
                ->where(fn ($q) => $q->whereNull('whatsapp')->orWhere('whatsapp', ''))->count(), BranchResource::getUrl('index')],
            ['Artikel terbit tanpa foto sampul', Article::query()->published()
                ->where(fn ($q) => $q->whereNull('cover_path')->orWhere('cover_path', ''))->count(), ArticleResource::getUrl('index')],
            ['Menu aktif tanpa foto', MenuItem::query()->where('is_active', true)
                ->where(fn ($q) => $q->whereNull('image_path')->orWhere('image_path', ''))->count(), MenuItemResource::getUrl('index')],
            ['Lowongan aktif yang batasnya sudah lewat', JobOpening::query()->where('is_active', true)
                ->whereNotNull('deadline')->whereDate('deadline', '<', now())->count(), JobOpeningResource::getUrl('index')],
        ];

        return ['items' => array_values(array_filter($items, fn (array $item) => $item[1] > 0))];
    }
}