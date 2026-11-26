<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Branch;
use App\Models\Faq;
use App\Models\JobOpening;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Data CONTOH untuk mengisi website saat pengembangan.
 *
 * Penanda data contoh:
 *  - Cabang, menu, lowongan, FAQ : sort_order >= 900
 *  - Artikel                      : slug diawali "contoh-"
 *  - Gambar                       : storage/app/public/dummy/
 *
 * Aman dijalankan berulang kali (tidak membuat data ganda).
 * Hapus semuanya dengan: php artisan db:seed --class=ClearDummyContentSeeder
 */
class DummyContentSeeder extends Seeder
{
    private const MARK = 900;

    public function run(): void
    {
        $this->menu();
        $this->branches();
        $this->jobs();
        $this->articles();
        $this->faqs();

        $this->command?->info('Data contoh selesai dibuat.');
        $this->command?->info('Hapus nanti dengan: php artisan db:seed --class=ClearDummyContentSeeder');
    }

    private function menu(): void
    {
        // [nama, deskripsi, harga (contoh), label, tampil di Beranda, warna makanan]
        $items = [
            ['Lobster saos padang', 'Daging lobster segar dalam saos padang yang pedas dan kental.', 185000, 'Terlaris', true, [200, 60, 40]],
            ['Udang asam manis pedas', 'Udang besar dengan saus asam manis pedas yang meresap.', 95000, null, true, [190, 90, 40]],
            ['Kepiting mix spesial', 'Kepiting, cumi, dan kerang dalam satu hidangan penuh rasa.', 165000, null, true, [170, 70, 50]],
            ['Kerapu bakar', 'Dibakar harum, disajikan dengan sambal dan lalapan segar.', 120000, null, true, [120, 80, 40]],
            ['Gurame mix udang cumi', 'Gurame renyah ditemani udang dan cumi dalam bumbu gurih.', 110000, null, false, [190, 120, 50]],
            ['Kerang dara saus asam manis', 'Kerang dara montok dengan saus asam manis yang segar.', 55000, null, false, [150, 60, 40]],
            ['Kerang hijau saus lada hitam', 'Kerang hijau gurih dengan aroma lada hitam yang kuat.', 60000, 'Baru', false, [60, 50, 40]],
        ];

        foreach ($items as $i => [$name, $desc, $price, $badge, $home, $food]) {
            MenuItem::updateOrCreate(
                ['name' => $name],
                [
                    'category' => 'seafood',
                    'description' => $desc,
                    'price' => $price,
                    'image_path' => $this->image("dummy/menu-{$i}.jpg", 800, 1000, [226, 196, 150], [122, 82, 44], $food),
                    'badge' => $badge,
                    'show_on_home' => $home,
                    'is_active' => true,
                    'sort_order' => self::MARK + $i,
                ]
            );
        }
    }

    private function branches(): void
    {
        // Titik koordinat hanya PERKIRAAN pusat kota. Ganti dengan koordinat asli di admin.
        $branches = [
            ['Seafood Muphus Babat', 'Lamongan', 'Jl. Raya Contoh No. 1, Babat, Lamongan', -7.1136, 112.1736],
            ['Seafood Muphus Gresik', 'Gresik', 'Jl. Contoh No. 2, Gresik', -7.1561, 112.6551],
            ['Seafood Muphus Unesa', 'Surabaya', 'Jl. Contoh No. 3, Ketintang, Surabaya', -7.3087, 112.7287],
            ['Seafood Muphus Malang Akordion', 'Malang', 'Jl. Contoh No. 4, Malang', -7.9839, 112.6214],
            ['Seafood Muphus Probolinggo', 'Probolinggo', 'Jl. Contoh No. 5, Probolinggo', -7.7543, 113.2159],
        ];

        foreach ($branches as $i => [$name, $city, $address, $lat, $lng]) {
            Branch::updateOrCreate(
                ['name' => $name],
                [
                    'city' => $city,
                    'address' => $address,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'maps_url' => null,
                    'whatsapp' => '62812000000' . ($i + 1), // nomor palsu, jangan dihubungi
                    'opens_at' => '10:00',
                    'closes_at' => '22:00',
                    'hours_note' => null,
                    'photo_path' => null,
                    'is_active' => true,
                    'sort_order' => self::MARK + $i,
                ]
            );
        }
    }

    private function jobs(): void
    {
        $jobs = [
            [
                'type' => 'outlet', 'position' => 'Crew Outlet Salatiga', 'city' => 'Salatiga', 'division' => null,
                'hook' => 'Jadi bagian dari tim outlet baru Muphus di Salatiga.',
                'summary' => '8 posisi: kepala warung, kasir, koki, bartender, helper, dan waiter. Siap training di Ungaran atau Solo.',
                'deadline' => now()->addDays(30)->toDateString(),
            ],
            [
                'type' => 'outlet', 'position' => 'Tim Outlet Sragen', 'city' => 'Sragen', 'division' => null,
                'hook' => 'Bangun karir bersama outlet Muphus di Sragen.',
                'summary' => '5 posisi: pramusaji dan bartender, juru masak, kasir, admin, dan kepala warung.',
                'deadline' => null,
            ],
            [
                'type' => 'office', 'position' => 'Staff Finance', 'city' => null, 'division' => 'Finance',
                'hook' => 'Jaga keuangan setiap unit tetap rapi dan akurat.',
                'summary' => 'Menyusun laporan keuangan unit, rekonsiliasi, dan audit keuangan ke unit-unit Muphus.',
                'deadline' => null,
            ],
        ];

        foreach ($jobs as $i => $job) {
            JobOpening::updateOrCreate(
                ['position' => $job['position']],
                $job + [
                    'brochure_path' => $this->image("dummy/job-{$i}.jpg", 750, 1000, [20, 124, 36], [12, 52, 20], null, true),
                    'form_url' => 'https://forms.gle/contoh-' . ($i + 1), // tautan contoh, belum nyata
                    'is_active' => true,
                    'sort_order' => self::MARK + $i,
                ]
            );
        }
    }

    private function articles(): void
    {
        $body = <<<'MD'
Ada kesenangan tersendiri saat sepiring seafood hadir di tengah meja. Tangan mulai meraih hidangan, nasi hangat berpindah piring, dan cerita mengalir di sela suapan.

## Mulai dari bahan yang akrab

Udang, cumi, kerang, dan ikan punya daya tarik masing-masing. Pilih beberapa olahan dengan karakter berbeda agar setiap orang punya pilihan.

## Susun paduan rasa yang nyaman

Mulailah dengan menanyakan makanan yang disukai, tingkat kepedasan, dan pantangan setiap orang. Sambal sebaiknya disajikan terpisah.

> Menu terbaik untuk dibagi adalah menu yang membuat semua orang merasa ikut dipikirkan.

Tulisan ini adalah **contoh isi artikel**. Ganti dengan tulisan asli lewat panel admin.
MD;

        // [judul, slug, kategori, ringkasan, unggulan]
        $articles = [
            ['Seafood Muphus – Deretan Menu Favorit Seafood', 'contoh-deretan-menu-favorit-seafood', 'menu', 'Dari udang hingga ikan bakar, temukan inspirasi menyusun hidangan seafood untuk berbagi rasa dan cerita bersama keluarga.', true],
            ['Dari Satu Tenda di Babat ke Puluhan Kota', 'contoh-dari-babat-ke-puluhan-kota', 'cerita', 'Perjalanan Seafood Muphus sejak outlet pertama dibuka di Babat, Lamongan, pada 26 Januari 2022.', false],
            ['Cara Memilih Seafood Segar', 'contoh-cara-memilih-seafood-segar', 'tips', 'Kenali ciri seafood yang masih segar sebelum Anda membelinya.', false],
            ['Sambal dan Pelengkap, Teman Satu Hidangan', 'contoh-sambal-dan-pelengkap', 'menu', 'Sentuhan sederhana untuk menyeimbangkan rasa di meja makan keluarga.', false],
            ['Makan Bersama Tanpa Terburu-buru', 'contoh-makan-bersama-tanpa-terburu-buru', 'kabar', 'Hal-hal kecil yang membuat waktu makan menjadi momen yang dinantikan.', false],
        ];

        foreach ($articles as $i => [$title, $slug, $category, $excerpt, $featured]) {
            Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'category' => $category,
                    'excerpt' => $excerpt,
                    'body' => $body,
                    'cover_path' => $this->image("dummy/artikel-{$i}.jpg", 1200, 800, [236, 212, 172], [60, 110, 70], [200, 90, 50]),
                    'status' => 'published',
                    'published_at' => now()->subDays(($i + 1) * 3),
                    'is_featured' => $featured,
                    'seo_title' => null,
                    'seo_description' => null,
                ]
            );
        }
    }

    private function faqs(): void
    {
        $faqs = [
            ['karir', 'Apa bedanya lowongan Outlet dan Office?', 'Outlet berarti bekerja langsung di outlet Muphus, misalnya sebagai kasir, koki, atau waiter. Office berarti bekerja di kantor pusat Tuban, misalnya di bagian finance.'],
            ['karir', 'Bagaimana cara mendaftar?', 'Klik Daftar sekarang pada lowongan yang Anda minati, lalu isi formulirnya. Tim HRD akan menghubungi Anda.'],
            ['karir', 'Apakah saya harus mengikuti training?', 'Beberapa lowongan outlet mewajibkan training, misalnya di Ungaran atau Solo. Syaratnya tertera di brosur tiap lowongan.'],
            ['karir', 'Apakah benefitnya sama untuk semua posisi?', 'Benefit dapat berbeda di tiap posisi dan outlet. Cek brosur lowongan, atau tanyakan ke tim HRD.'],
            ['karir', 'Bagaimana jika belum ada posisi yang cocok?', 'Kirim CV Anda ke hrdmuphusseafood@gmail.com. Kami simpan untuk kesempatan berikutnya.'],
            ['kontak', 'Di mana cabang Muphus terdekat?', 'Muphus hadir di 31 cabang di Jawa Timur dan Jawa Tengah. Cari yang terdekat di halaman Cabang.'],
            ['kontak', 'Bagaimana cara melamar kerja?', 'Buka halaman Karir, pilih lowongan, lalu klik Daftar sekarang.'],
            ['kontak', 'Apakah bisa pesan online atau dibungkus?', 'Bisa. Muphus melayani makan di tempat, dibungkus, maupun pesan online.'],
        ];

        foreach ($faqs as $i => [$page, $question, $answer]) {
            Faq::updateOrCreate(
                ['page' => $page, 'question' => $question],
                ['answer' => $answer, 'sort_order' => self::MARK + $i, 'is_active' => true]
            );
        }
    }

    /**
     * Buat gambar placeholder (gradien + piring). Butuh ekstensi GD.
     * Mengembalikan jalur relatif di disk "public", atau null bila GD tidak aktif.
     */
    private function image(string $path, int $w, int $h, array $top, array $bottom, ?array $food = null, bool $paper = false): ?string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $img = imagecreatetruecolor($w, $h);

        for ($y = 0; $y < $h; $y++) {
            $t = $y / max(1, $h - 1);
            $color = imagecolorallocate(
                $img,
                (int) ($top[0] + ($bottom[0] - $top[0]) * $t),
                (int) ($top[1] + ($bottom[1] - $top[1]) * $t),
                (int) ($top[2] + ($bottom[2] - $top[2]) * $t)
            );
            imageline($img, 0, $y, $w, $y, $color);
        }

        if ($food) {
            imagefilledellipse($img, (int) ($w * 0.5), (int) ($h * 0.62), (int) ($w * 0.84), (int) ($h * 0.44), imagecolorallocate($img, 244, 244, 240));
            imagefilledellipse($img, (int) ($w * 0.5), (int) ($h * 0.62), (int) ($w * 0.62), (int) ($h * 0.3), imagecolorallocate($img, $food[0], $food[1], $food[2]));
        }

        if ($paper) {
            imagefilledrectangle($img, (int) ($w * 0.1), (int) ($h * 0.1), (int) ($w * 0.9), (int) ($h * 0.9), imagecolorallocate($img, 244, 252, 244));
            imagefilledrectangle($img, (int) ($w * 0.1), (int) ($h * 0.1), (int) ($w * 0.9), (int) ($h * 0.26), imagecolorallocate($img, 20, 124, 36));
            imagefilledrectangle($img, (int) ($w * 0.16), (int) ($h * 0.34), (int) ($w * 0.84), (int) ($h * 0.38), imagecolorallocate($img, 200, 215, 200));
            imagefilledrectangle($img, (int) ($w * 0.16), (int) ($h * 0.44), (int) ($w * 0.7), (int) ($h * 0.48), imagecolorallocate($img, 200, 215, 200));
            imagefilledrectangle($img, (int) ($w * 0.16), (int) ($h * 0.54), (int) ($w * 0.76), (int) ($h * 0.58), imagecolorallocate($img, 200, 215, 200));
        }

        ob_start();
        imagejpeg($img, null, 82);
        $data = ob_get_clean();

        Storage::disk('public')->put($path, $data);

        return $path;
    }
}