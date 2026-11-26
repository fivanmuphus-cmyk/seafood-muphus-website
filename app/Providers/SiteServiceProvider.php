<?php

namespace App\Providers;

use App\Support\Site;
use Illuminate\Support\ServiceProvider;

class SiteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Pengaturan umum dari admin menimpa config/muphus.php, jadi semua tampilan lama ikut berubah.
        Site::applyToConfig();
    }
}