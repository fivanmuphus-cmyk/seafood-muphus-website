<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

/**
 * Merapikan semua kartu menu sekaligus: nama, subkategori, urutan, "Andalan", dan "Beranda".
 *  - TIDAK mengubah "Aktif", foto, maupun deskripsi. Kartu tetap nonaktif sampai Anda unggah foto lalu menyalakannya.
 *  - Kelompok "Daftar menu lengkap" (urutan 100 ke atas) tidak disentuh.
 *  - Pelengkap dan sayur dikeluarkan dari "Menu andalan" (Andalan dimatikan).
 *  - Beranda hanya 4 kartu: Kepiting Mix Spesial, Lobster Bakar, Kepiting Saus Padang, Kerapu Bakar.
 * Nama dicocokkan tanpa memperhatikan besar-kecil huruf dan kata "saus"/"saos". Aman dijalankan berulang.
 *
 * Jalankan: php artisan db:seed --class=MenuTidyUpSeeder
 */
class MenuTidyUpSeeder extends Seeder
{
    public function run(): void
    {
        $cards = MenuItem::where('show_in_list', false)->get();
        $missing = [];
        $done = 0;

        foreach ($this->plan() as $i => [$name, $category, $subcategory, $home, $andalan]) {
            $item = $cards->first(fn ($m) => self::key($m->name) === self::key($name));

            if (! $item) {
                $missing[] = $name;
                continue;
            }

            $item->update([
                'name' => $name,
                'category' => $category,
                'subcategory' => $subcategory,
                'show_as_card' => $andalan,
                'show_on_home' => $home,
                'sort_order' => $i + 1,
            ]);
            $done++;
        }

        $this->command?->info("{$done} kartu dirapikan.");

        if ($missing) {
            $this->command?->warn('Tidak ditemukan di admin (jalankan MenuFeaturedSeeder dulu): ' . implode(', ', $missing));
        }
    }

    private static function key(string $name): string
    {
        return preg_replace('/[^a-z0-9]/', '', preg_replace('/\bsa[uo]s\b/', '', mb_strtolower($name)));
    }

    /** Urutan tampil = urutan di sini. [nama, kategori, subkategori, Beranda?, Andalan?] */
    private function plan(): array
    {
        $mix = ['seafood', 'mix-spesial'];
        $kl = ['seafood', 'kepiting-lobster'];
        $ikan = ['seafood', 'ikan'];
        $cuk = ['seafood', 'cumi-udang-kerang'];
        $ab = ['ayam-bebek', null];

        return [
            ['Kepiting Mix Spesial', ...$mix, true, true],
            ['Gurame Mix Spesial', ...$mix, false, true],
            ['Gurame Mix Seafood', ...$mix, false, true],

            ['Kepiting Saus Padang', ...$kl, true, true],
            ['Kepiting Asam Manis', ...$kl, false, true],
            ['Lobster Bakar', ...$kl, true, true],
            ['Lobster Saus Padang', ...$kl, false, true],

            ['Kerapu Bakar', ...$ikan, true, true],
            ['Kerapu Saus Asam Manis Pedas', ...$ikan, false, true],
            ['Kakap Saus Padang', ...$ikan, false, true],
            ['Kakap Bakar', ...$ikan, false, true],
            ['Gurame Bakar', ...$ikan, false, true],
            ['Gurame Saus Asam Manis', ...$ikan, false, true],
            ['Bawal Saus Tiram', ...$ikan, false, true],
            ['Lele Bakar', ...$ikan, false, true],
            ['Lele Saus Padang', ...$ikan, false, true],
            ['Lele Saus Lada Hitam', ...$ikan, false, true],
            ['Lele Saus Asam Manis Pedas', ...$ikan, false, true],

            ['Cumi Asam Manis', ...$cuk, false, true],
            ['Cumi Saus Padang', ...$cuk, false, true],
            ['Cumi Crispy Saus Padang', ...$cuk, false, true],
            ['Cumi Goreng Kremes', ...$cuk, false, true],
            ['Udang Asam Manis Pedas', ...$cuk, false, true],
            ['Udang Saus Padang', ...$cuk, false, true],
            ['Udang Saus Lada Hitam', ...$cuk, false, true],
            ['Udang Crispy', ...$cuk, false, true],
            ['Udang Crispy Saus Lada Hitam', ...$cuk, false, true],
            ['Kerang Dara Saus Asam Manis', ...$cuk, false, true],

            ['Ayam Goreng Kremes', ...$ab, false, true],
            ['Ayam Saus Asam Manis Pedas', ...$ab, false, true],
            ['Bebek Goreng Kremes', ...$ab, false, true],
            ['Bebek Bakar', ...$ab, false, true],

            // Sayur dan pelengkap: bukan hidangan andalan, jadi tidak masuk "Menu andalan".
            ['Cah Tauge Spesial Udang', 'sayur', null, false, false],
            ['Cah Kangkung Polos', 'sayur', null, false, false],
            ['Cah Kangkung Spesial Udang', 'sayur', null, false, false],
            ['Kentang Goreng', 'pelengkap', null, false, false],
            ['Tempe Goreng', 'pelengkap', null, false, false],
            ['Tahu Goreng', 'pelengkap', null, false, false],
            ['Sambal Kecap', 'pelengkap', null, false, false],
        ];
    }
}