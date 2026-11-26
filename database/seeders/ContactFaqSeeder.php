<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

/**
 * FAQ untuk halaman Kontak. Isinya hanya hal yang sudah pasti.
 * Jalankan: php artisan db:seed --class=ContactFaqSeeder
 * Ubah atau tambah kapan saja lewat menu FAQ di admin (halaman: Kontak).
 */
class ContactFaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['Di mana cabang Muphus terdekat?', 'Seafood Muphus hadir di berbagai kota di Indonesia. Cari yang terdekat dari Anda di halaman Cabang.'],
            ['Bagaimana cara melamar kerja?', 'Buka halaman Karir, pilih lowongan yang Anda minati, lalu klik Daftar sekarang dan isi formulirnya.'],
            ['Bagaimana cara membuka kemitraan?', 'Hubungi kami lewat WhatsApp, lalu sampaikan minat dan lokasi lahan Anda. Kami akan menjelaskan langkah berikutnya.'],
            ['Apakah bisa pesan online atau dibungkus?', 'Bisa. Seafood Muphus melayani makan di tempat, dibungkus, maupun pesan online.'],
        ];

        foreach ($faqs as $i => [$question, $answer]) {
            Faq::updateOrCreate(
                ['page' => 'kontak', 'question' => $question],
                ['answer' => $answer, 'sort_order' => $i, 'is_active' => true]
            );
        }
    }
}