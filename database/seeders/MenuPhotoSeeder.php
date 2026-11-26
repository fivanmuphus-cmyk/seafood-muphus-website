<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Memasang foto awal pada kelompok di "Daftar menu lengkap".
 * Foto diambil dari gambar menu cetak (folder database/seeders/menu-photos).
 * Hanya mengisi kelompok yang belum punya foto, jadi foto unggahan admin tidak tertimpa.
 *
 * Jalankan: php artisan db:seed --class=MenuPhotoSeeder
 */
class MenuPhotoSeeder extends Seeder
{
    public function run(): void
    {
        // nama kelompok di database => berkas foto
        $photos = [
            'Kepiting mix' => 'kepiting-mix',
            'Gurame mix' => 'gurame-mix',
            'Kepiting' => 'kepiting',
            'Lobster' => 'lobster',
            'Gurame' => 'gurame',
            'Bawal' => 'bawal',
            'Kakap' => 'kakap',
            'Kerapu' => 'kerapu',
            'Cumi' => 'cumi',
        ];

        $filled = 0;

        foreach ($photos as $name => $file) {
            $source = database_path("seeders/menu-photos/{$file}.webp");
            $item = MenuItem::where('name', $name)->where('show_in_list', true)->first();

            if (! $item || $item->image_path || ! is_file($source)) {
                continue;
            }

            $path = "menu/{$file}.webp";
            Storage::disk('public')->put($path, file_get_contents($source), 'public');
            $item->update(['image_path' => $path]);
            $filled++;
        }

        $this->command?->info("Foto terpasang pada {$filled} kelompok menu.");

        $missing = MenuItem::where('show_in_list', true)->whereNull('image_path')->pluck('name')->all();
        if ($missing) {
            $this->command?->warn('Belum punya foto: ' . implode(', ', $missing));
        }
    }
}