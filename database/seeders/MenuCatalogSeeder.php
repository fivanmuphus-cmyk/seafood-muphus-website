<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

/**
 * Daftar menu lengkap, disalin dari gambar menu cetak "Region Jatim" (depan dan belakang).
 * PENTING: dibaca dari gambar, jadi periksa ulang SETIAP harga di admin sebelum tayang.
 *
 * Aman dijalankan berulang (tidak membuat data ganda).
 * Jalankan: php artisan db:seed --class=MenuCatalogSeeder
 */
class MenuCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $this->repairSignatureCards();
        $this->classifySignatureCards();

        $order = 100;

        foreach ($this->groups() as $group) {
            [$category, $subcategory, $name, $price, $priceMax, $priceNote, $variants] = $group;

            MenuItem::updateOrCreate(
                ['name' => $name, 'category' => $category, 'show_in_list' => true],
                [
                    'subcategory' => $subcategory,
                    'price' => $price,
                    'price_max' => $priceMax,
                    'price_note' => $priceNote,
                    'variants' => $variants,
                    'show_as_card' => false,
                    'show_in_list' => true,
                    'show_on_home' => false,
                    'is_active' => true,
                    'sort_order' => $order++,
                ]
            );
        }

        $this->command?->info('Daftar menu lengkap selesai dibuat. Periksa ulang semua harga di admin.');
    }

    /**
     * Pemulihan: versi seeder sebelumnya bisa mengubah kartu andalan "Lobster", "Kerang Dara",
     * dan "Kerang Hijau" menjadi kelompok daftar. Kartu bergambar poster dikembalikan ke semula.
     * Aman dijalankan berulang; tidak berefek bila tidak ada yang perlu dipulihkan.
     */
    private function repairSignatureCards(): void
    {
        $originalOrder = ['Lobster' => 0, 'Kerang Dara' => 7, 'Kerang Hijau' => 8];

        foreach ($originalOrder as $name => $sort) {
            MenuItem::where('name', $name)
                ->where('show_in_list', true)
                ->where('image_path', 'like', 'seed/menu/%')
                ->update([
                    'show_as_card' => true,
                    'show_in_list' => false,
                    'variants' => null,
                    'price' => null,
                    'price_max' => null,
                    'price_note' => null,
                    'sort_order' => $sort,
                ]);
        }
    }

    /** Menu andalan bergambar (dari website lama): beri subkategori, dan rapikan deskripsi Kerapu Bakar. */
    private function classifySignatureCards(): void
    {
        $map = [
            'Lobster' => 'kepiting-lobster',
            'Kepiting Saus Padang' => 'kepiting-lobster',
            'Kepiting Mix Lengkap' => 'mix-spesial',
            'Gurame Mix Udang Cumi' => 'mix-spesial',
            'Kerapu Bakar' => 'ikan',
            'Gurame Bakar' => 'ikan',
            'Bawal Bakar' => 'ikan',
            'Kerang Dara' => 'cumi-udang-kerang',
            'Kerang Hijau' => 'cumi-udang-kerang',
        ];

        foreach ($map as $name => $subcategory) {
            MenuItem::where('name', $name)->where('show_in_list', false)->update([
                'category' => 'seafood',
                'subcategory' => $subcategory,
                'show_as_card' => true,
            ]);
        }

        MenuItem::where('name', 'Kerapu Bakar')->where('show_in_list', false)->update([
            'description' => 'Kerapu segar dengan daging tebal dan lembut, dibakar dengan bumbu khas yang meresap hingga ke dalam.',
        ]);
    }

    /** Satu varian: nama, harga (rupiah, boleh kosong), pedas, catatan. */
    private static function v(string $name, ?int $price = null, bool $spicy = false, ?string $note = null): array
    {
        return array_filter(
            ['name' => $name, 'price' => $price, 'spicy' => $spicy, 'note' => $note],
            fn ($value) => $value !== null && $value !== false
        );
    }

    /** Enam olahan khas untuk satu jenis ikan atau seafood. */
    private static function cooks(string $item, bool $withBakar = true): array
    {
        return array_values(array_filter([
            $withBakar ? self::v("{$item} bakar") : null,
            self::v("{$item} goreng kremes"),
            self::v("{$item} saus padang", null, true),
            self::v("{$item} saus asam manis / pedas", null, true),
            self::v("{$item} saus tiram"),
            self::v("{$item} saus lada hitam"),
        ]));
    }

    /** Empat saus berharga sama (untuk cumi, udang, kerang). */
    private static function sauces(string $item, int $price): array
    {
        return [
            self::v("{$item} saus padang", $price, true),
            self::v("{$item} saus asam manis / pedas", $price, true),
            self::v("{$item} saus tiram", $price),
            self::v("{$item} saus lada hitam", $price),
        ];
    }

    /** Versi crispy: satu goreng tepung 30K, empat saus 35K. */
    private static function crispy(string $item): array
    {
        return [
            self::v("{$item} crispy goreng tepung", 30000),
            self::v("{$item} crispy saus padang", 35000, true),
            self::v("{$item} crispy saus asam manis / pedas", 35000, true),
            self::v("{$item} crispy saus tiram", 35000),
            self::v("{$item} crispy saus lada hitam", 35000),
        ];
    }

    /** Ayam atau bebek: kremes, bakar, lalu empat saus. */
    private static function poultry(string $item, int $kremes, int $bakar, int $sauce): array
    {
        return [
            self::v("{$item} goreng kremes", $kremes),
            self::v("{$item} bakar", $bakar),
            self::v("{$item} saus padang", $sauce, true),
            self::v("{$item} saus asam manis / pedas", $sauce, true),
            self::v("{$item} saus tiram", $sauce),
            self::v("{$item} saus lada hitam", $sauce),
        ];
    }

    /** Daftar paket: hanya nama isi, tanpa harga per baris. */
    private static function items(array $names): array
    {
        return array_map(fn (string $name) => self::v($name), $names);
    }

    /** [kategori, subkategori, nama, harga, harga maks, satuan, varian] */
    private function groups(): array
    {
        return [
            // ---------------------------------------------------- SEAFOOD: MIX SPESIAL
            ['seafood', 'mix-spesial', 'Gurame mix', null, null, null, [
                self::v('Gurame mix spesial', 140000, false, '1 gurame + kerang hijau + kerang dara + udang + cumi + jagung'),
                self::v('Gurame mix seafood', 120000, false, '1 gurame + udang + cumi + jagung'),
                self::v('Gurame mix kerang', 110000, false, '1 gurame + kerang hijau + kerang dara + jagung'),
            ]],
            ['seafood', 'mix-spesial', 'Kepiting mix', null, null, null, [
                self::v('Kepiting mix spesial', 150000, false, '1 kepiting + kerang hijau + kerang dara + udang + cumi + jagung'),
                self::v('Kepiting mix seafood', 125000, false, '1 kepiting + udang + cumi + jagung'),
                self::v('Kepiting mix kerang', 110000, false, '1 kepiting + kerang hijau + kerang dara + jagung'),
            ]],

            // ---------------------------------------------------- SEAFOOD: KEPITING DAN LOBSTER
            ['seafood', 'kepiting-lobster', 'Kepiting', 110000, 150000, null, [
                self::v('Kepiting saus asam manis / pedas', null, true),
                self::v('Kepiting saus padang', null, true),
                self::v('Kepiting saus tiram'),
                self::v('Kepiting saus lada hitam'),
            ]],
            ['seafood', 'kepiting-lobster', 'Lobster', 50000, null, 'ons', [
                self::v('Lobster bakar'),
                self::v('Lobster saus asam manis / pedas', null, true),
                self::v('Lobster saus padang', null, true),
                self::v('Lobster saus tiram'),
                self::v('Lobster saus lada hitam'),
            ]],

            // ---------------------------------------------------- SEAFOOD: IKAN
            ['seafood', 'ikan', 'Gurame', 50000, 65000, null, self::cooks('Gurame')],
            ['seafood', 'ikan', 'Bawal', 55000, 70000, null, self::cooks('Bawal')],
            ['seafood', 'ikan', 'Kakap', 50000, 75000, null, self::cooks('Kakap')],
            ['seafood', 'ikan', 'Kerapu', 55000, 75000, null, self::cooks('Kerapu')],
            ['seafood', 'ikan', 'Lele', null, null, null, [
                self::v('Lele goreng kremes', 11000),
                self::v('Lele bakar', 13000),
                self::v('Lele saus padang', 15000, true),
                self::v('Lele saus asam manis / pedas', 15000, true),
                self::v('Lele saus tiram', 15000),
                self::v('Lele saus lada hitam', 15000),
            ]],

            // ---------------------------------------------------- SEAFOOD: CUMI, UDANG, KERANG
            ['seafood', 'cumi-udang-kerang', 'Cumi', null, null, null, self::sauces('Cumi', 35000)],
            ['seafood', 'cumi-udang-kerang', 'Cumi crispy', null, null, null, self::crispy('Cumi')],
            ['seafood', 'cumi-udang-kerang', 'Udang', null, null, null, self::sauces('Udang', 35000)],
            ['seafood', 'cumi-udang-kerang', 'Udang crispy', null, null, null, self::crispy('Udang')],
            ['seafood', 'cumi-udang-kerang', 'Kerang hijau', null, null, null, self::sauces('Kerang hijau', 25000)],
            ['seafood', 'cumi-udang-kerang', 'Kerang dara', null, null, null, self::sauces('Kerang dara', 25000)],

            // ---------------------------------------------------- PAKET KELUARGA
            ['paket', null, 'Paket A', 188000, null, null, self::items([
                'Ayam goreng kremes (3)', 'Gurame bakar', 'Cah tauge', 'Sambal lalapan', 'Nasi (5)', 'Es teh (5)',
            ])],
            ['paket', null, 'Paket B', 285000, null, null, self::items([
                'Kepiting masak saus', 'Kakap bakar', 'Udang goreng tepung', 'Kerang hijau asam manis',
                'Cah kangkung', 'Sambal lalapan', 'Nasi (5)', 'Es teh (5)',
            ])],
            ['paket', null, 'Paket C', 589000, null, null, self::items([
                'Kepiting mix spesial', 'Udang tepung siram saus', 'Cumi tepung siram saus', 'Kakap bakar', 'Gurame bakar',
                'Ayam goreng kremes (4)', 'Cah kangkung (2)', 'Cah tauge (2)', 'Kentang (2)', 'Sambal lalapan',
                'Nasi (10)', 'Es teh (10)',
            ])],
            ['paket', null, 'Paket D', 699000, null, null, self::items([
                'Kepiting mix spesial', 'Kakap bakar', 'Gurame bakar', 'Kerapu saus asam manis', 'Ayam goreng kremes (4)',
                'Cumi saus padang', 'Udang saus padang', 'Kerang dara saus padang', 'Udang goreng tepung',
                'Cah kangkung (2)', 'Cah tauge (2)', 'Kentang (2)', 'Sambal lalapan',
                'Nasi (10)', 'Es teh (5)', 'Jeruk (5)',
            ])],

            // ---------------------------------------------------- AYAM DAN BEBEK
            ['ayam-bebek', null, 'Ayam', null, null, null, self::poultry('Ayam', 21000, 24000, 27000)],
            ['ayam-bebek', null, 'Bebek', null, null, null, self::poultry('Bebek', 30000, 33000, 33000)],

            // ---------------------------------------------------- SAYUR
            ['sayur', null, 'Cah tauge', null, null, null, [
                self::v('Cah tauge polos', 11000),
                self::v('Cah tauge spesial udang', 25000),
                self::v('Cah tauge spesial cumi', 25000),
            ]],
            ['sayur', null, 'Cah kangkung', null, null, null, [
                self::v('Cah kangkung polos', 11000),
                self::v('Cah kangkung terasi', 11000),
                self::v('Cah kangkung spesial udang', 25000),
                self::v('Cah kangkung spesial cumi', 25000),
            ]],

            // ---------------------------------------------------- PELENGKAP
            ['pelengkap', null, 'Lain-lain', null, null, null, [
                self::v('Nasi putih', 5000),
                self::v('Tahu', 6000),
                self::v('Tempe', 7000),
                self::v('Kentang goreng', 11000),
            ]],

            // ---------------------------------------------------- MINUMAN
            ['minuman', null, 'Minuman', null, null, null, [
                self::v('Air mineral', 5000),
                self::v('Teh manis es / panas', 5000),
                self::v('Kopi', 5000),
                self::v('Lemon tea es / panas', 6000),
                self::v('Jeruk es / panas', 6000),
                self::v('Susu putih', 5000),
                self::v('Susu coklat', 6000),
                self::v('Teh manis jumbo es', 8000),
                self::v('Jeruk jumbo es', 8000),
                self::v('Lemon tea jumbo es', 8000),
            ]],
        ];
    }
}