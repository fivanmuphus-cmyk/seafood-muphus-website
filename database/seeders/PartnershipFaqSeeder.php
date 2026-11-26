<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

/**
 * FAQ halaman Kemitraan. Isinya hanya ketentuan yang tertulis di dokumen Partnership.
 * Jalankan: php artisan db:seed --class=PartnershipFaqSeeder
 * Ubah atau tambah kapan saja lewat menu FAQ di admin (halaman: Kemitraan).
 */
class PartnershipFaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['Apakah ada biaya kemitraan?', 'Tidak. Dalam skema kerja sama saat ini, kemitraan tidak dikenakan biaya.'],
            ['Siapa yang mengelola outlet?', 'Selama masa kerja sama, pengelolaan operasional outlet dipegang oleh manajemen Muphus Group.'],
            ['Berapa lama masa sewa lahan?', 'Durasi sewa lahan minimal 5 tahun, dan disepakati bersama pemilik lahan.'],
            ['Bagaimana cara memulai?', 'Hubungi kami lewat WhatsApp atau formulir Kontak, lalu sampaikan lokasi dan ukuran lahan Anda. Verifikator kami akan memeriksa kesesuaiannya dengan standar Muphus.'],
        ];

        foreach ($faqs as $i => [$question, $answer]) {
            Faq::updateOrCreate(
                ['page' => 'kemitraan', 'question' => $question],
                ['answer' => $answer, 'sort_order' => $i, 'is_active' => true]
            );
        }
    }
}