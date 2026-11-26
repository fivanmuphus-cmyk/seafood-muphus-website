<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * Mengisi ID pengukuran Google Analytics ke Pengaturan Umum di admin.
 * Hanya kolom itu yang diubah. Jalankan: php artisan db:seed --class=AnalyticsSeeder
 */
class AnalyticsSeeder extends Seeder
{
    public function run(): void
    {
        $row = SiteSetting::firstOrCreate(['group' => 'general']);

        $row->data = array_merge((array) $row->data, ['analytics_id' => 'G-FHB80VSTQ0']);
        $row->save();

        $this->command?->info('ID Google Analytics G-FHB80VSTQ0 sudah diisi.');
    }
}
