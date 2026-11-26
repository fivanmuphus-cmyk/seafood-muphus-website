<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * Mengisi nomor WhatsApp (kemitraan dan HRD) serta akun media sosial ke Pengaturan Umum di admin.
 * Hanya kolom di bawah ini yang diubah; isian lain di Pengaturan Umum tidak disentuh.
 * "WhatsApp admin (umum)" sengaja tidak diubah. Isi sendiri di admin bila nomornya sudah ada.
 *
 * Jalankan: php artisan db:seed --class=ContactSocialSeeder
 */
class ContactSocialSeeder extends Seeder
{
    public function run(): void
    {
        $row = SiteSetting::firstOrCreate(['group' => 'general']);

        $row->data = array_merge((array) $row->data, [
            'email' => 'indonesiamenembusgravitasi2@gmail.com',
            'partnership_whatsapp' => '6281130878783',
            'partnership_whatsapp_display' => '0811 3087 8783',
            'hr_whatsapp' => '628113049145',
            'hr_whatsapp_display' => '0811 3049 145',
            'hr_instagram' => 'lokerseafoodmuphus',
            'instagram' => 'https://www.instagram.com/seafoodmuphusofficial/',
            'facebook' => 'https://www.facebook.com/share/1DdWNVt2rn/',
            'tiktok' => 'https://www.tiktok.com/@seafoodmuphusofficial',
            'threads' => 'https://www.threads.com/@seafoodmuphusofficial',
            'youtube' => 'https://www.youtube.com/@seafoodmuphusofficial',
        ]);
        $row->save();

        $this->command?->info('Email, nomor kemitraan, nomor HRD, dan 5 akun media sosial sudah diisi.');
    }
}
