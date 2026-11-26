<?php

namespace Database\Seeders;

use App\Models\PageBanner;
use Illuminate\Database\Seeder;

/**
 * Membuat satu baris banner untuk tiap halaman (admin tinggal mengganti foto).
 * Aman dijalankan berulang: tidak menimpa foto atau teks yang sudah diisi.
 * Jalankan: php artisan db:seed --class=PageBannerSeeder
 */
class PageBannerSeeder extends Seeder
{
    public function run(): void
    {
        foreach (array_keys(PageBanner::PAGES) as $page) {
            PageBanner::firstOrCreate(['page' => $page]);
        }
    }
}