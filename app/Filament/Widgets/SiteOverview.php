<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Branches\BranchResource;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\JobOpenings\JobOpeningResource;
use App\Models\Article;
use App\Models\Branch;
use App\Models\ContactMessage;
use App\Models\JobOpening;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $unread = ContactMessage::query()->whereNull('read_at')->count();

        return [
            Stat::make('Pesan belum dibaca', $unread)
                ->description($unread > 0 ? 'Perlu ditindaklanjuti' : 'Semua sudah dibaca')
                ->descriptionIcon($unread > 0 ? 'heroicon-m-envelope' : 'heroicon-m-check-circle')
                ->color($unread > 0 ? 'danger' : 'success')
                ->url(ContactMessageResource::getUrl('index')),
            Stat::make('Cabang aktif', Branch::query()->active()->count())
                ->description('Tampil di halaman Cabang')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('primary')
                ->url(BranchResource::getUrl('index')),
            Stat::make('Lowongan aktif', JobOpening::query()->open()->count())
                ->description('Tampil di halaman Karir')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary')
                ->url(JobOpeningResource::getUrl('index')),
            Stat::make('Artikel terbit', Article::query()->published()->count())
                ->description('Tampil di halaman Artikel')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('primary')
                ->url(ArticleResource::getUrl('index')),
        ];
    }
}