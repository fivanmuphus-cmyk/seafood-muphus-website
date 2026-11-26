<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

/**
 * Membuat kartu menu (nama + deskripsi, TANPA foto) dari daftar menu platting.
 *  - Kartu BARU dibuat NONAKTIF: belum tampil di website sampai Anda mengunggah foto lalu menyalakan "Aktif".
 *  - Kartu yang SUDAH ada tidak dibuat ulang; deskripsinya hanya diisi bila masih kosong.
 *  - Nama dicocokkan tanpa memperhatikan besar-kecil huruf dan kata "saus"/"saos".
 *
 * Aman dijalankan berulang. Jalankan: php artisan db:seed --class=MenuFeaturedSeeder
 */
class MenuFeaturedSeeder extends Seeder
{
    public function run(): void
    {
        $order = (int) MenuItem::max('sort_order') + 1;
        $existing = MenuItem::where('show_in_list', false)->get();
        $created = $filled = 0;
        $seen = [];

        foreach ($this->cards() as [$name, $description]) {
            $key = self::key($name);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $item = $existing->first(fn ($m) => self::key($m->name) === $key);

            if (! $item) {
                [$category, $subcategory] = self::classify($name);
                MenuItem::create([
                    'name' => $name,
                    'category' => $category,
                    'subcategory' => $subcategory,
                    'description' => $description,
                    'show_as_card' => true,
                    'show_in_list' => false,
                    'show_on_home' => false,
                    'is_active' => false, // nyalakan setelah foto diunggah
                    'sort_order' => $order++,
                ]);
                $created++;
            } elseif (blank($item->description)) {
                $item->update(['description' => $description]);
                $filled++;
            }
        }

        $this->command?->info("{$created} kartu baru (nonaktif, tunggu foto), {$filled} deskripsi kartu yang ada diisi.");
    }

    public static function key(string $name): string
    {
        return preg_replace('/[^a-z0-9]/', '', preg_replace('/\bsa[uo]s\b/', '', mb_strtolower($name)));
    }

    /** [kategori, subkategori] dari kata di nama. */
    private static function classify(string $name): array
    {
        $n = mb_strtolower($name);
        $has = fn (array $w) => (bool) array_filter($w, fn ($x) => str_contains($n, $x));

        if ($has(['cah '])) {
            return ['sayur', null];
        }
        if ($has(['tahu', 'tempe', 'kentang', 'sambal'])) {
            return ['pelengkap', null];
        }
        if ($has(['ayam', 'bebek'])) {
            return ['ayam-bebek', null];
        }
        if (str_contains($n, 'mix')) {
            return ['seafood', 'mix-spesial'];
        }
        if ($has(['kepiting', 'lobster'])) {
            return ['seafood', 'kepiting-lobster'];
        }
        if ($has(['cumi', 'udang', 'kerang'])) {
            return ['seafood', 'cumi-udang-kerang'];
        }

        return ['seafood', 'ikan'];
    }

    /** Daftar [nama, deskripsi]. Deskripsi hasil susunan otomatis dari bahan + cara olah, silakan disunting di admin. */
    private function cards(): array
    {
        // Deskripsi dari poster menu cetak, dipakai apa adanya.
        $poster = [
            ['Kepiting Mix Spesial', 'Hidangan seafood lengkap berisi kepiting, cumi, udang, dan kerang dengan racikan bumbu spesial yang kaya rasa.'],
            ['Gurame Mix Spesial', 'Gurame segar pilihan dipadukan dengan cumi, udang, dan kerang berkualitas dalam balutan saus spesial yang gurih dan kaya rasa. Perpaduan seafood lengkap dengan cita rasa istimewa dalam satu sajian.'],
            ['Kerapu Bakar', 'Kerapu segar pilihan dengan daging tebal dan lembut, dibakar sempurna menggunakan bumbu khas yang meresap hingga ke dalam, menghasilkan cita rasa gurih, harum, dan nikmat.'],
            ['Kakap Saus Padang', 'Kakap segar pilihan yang dimasak dengan saus Padang dari resep turun-temurun, menghadirkan perpaduan rasa gurih, pedas, dan kaya rempah yang khas serta menggugah selera.'],
            ['Cumi Asam Manis', 'Cumi segar pilihan dengan tekstur lembut dan kenyal, dipadukan dengan saus spesial racikan khas yang gurih, kaya rasa, dan menggugah selera di setiap gigitan.'],
        ];

        // Khusus: bukan pola "bahan + cara olah".
        $special = [
            'Gurame Mix Seafood' => 'Gurame segar bersama udang, cumi, dan jagung dalam balutan saus spesial yang gurih dan kaya rasa.',
            'Cah Tauge Spesial Udang' => 'Tauge segar ditumis cepat bersama udang hingga renyah, gurih, dan tetap segar.',
            'Cah Kangkung Polos' => 'Kangkung segar ditumis dengan bawang dan bumbu sederhana, renyah dan harum.',
            'Cah Kangkung Spesial Udang' => 'Kangkung segar ditumis bersama udang, renyah, gurih, dan harum bawang.',
            'Kentang Goreng' => 'Kentang goreng renyah di luar dan lembut di dalam, pelengkap yang pas untuk santap seafood.',
            'Tempe Goreng' => 'Tempe goreng gurih dan garing, teman nasi hangat dan sambal.',
            'Tahu Goreng' => 'Tahu goreng berkulit garing dan lembut di dalam, nikmat dengan sambal.',
            'Sambal Kecap' => 'Sambal kecap manis pedas dengan irisan cabai dan bawang, pendamping segala hidangan bakar dan goreng.',
            'Kepiting Asam Manis' => 'Kepiting segar berdaging manis dalam saus asam manis pedas yang segar dan menggugah selera.',
        ];

        $names = [
            'Bawal Saus Tiram', 'Bebek Goreng Kremes', 'Cumi Saus Padang', 'Kakap Bakar',
            'Lele Saus Lada Hitam', 'Lele Saus Padang', 'Lele Saus Asam Manis Pedas', 'Lobster Saus Padang',
            'Udang Crispy Saus Lada Hitam', 'Udang Saus Asam Manis Pedas', 'Udang Saus Padang', 'Ayam Goreng Kremes',
            'Ayam Saus Asam Manis Pedas', 'Bebek Bakar', 'Cumi Crispy Saus Padang', 'Cumi Goreng Kremes',
            'Gurame Bakar', 'Gurame Saus Asam Manis', 'Kepiting Saus Padang', 'Kerang Dara Saus Asam Manis',
            'Kerapu Saus Asam Manis Pedas', 'Lele Bakar', 'Lobster Bakar', 'Udang Crispy', 'Udang Saus Lada Hitam',
        ];

        $cards = $poster;
        foreach ($special as $name => $text) {
            $cards[] = [$name, $text];
        }
        foreach ($names as $name) {
            $cards[] = [$name, self::describe($name)];
        }

        return $cards;
    }

    /** Susun deskripsi dari bahan + cara olah di nama. */
    private static function describe(string $name): string
    {
        $n = mb_strtolower($name);
        $base = 'Hidangan pilihan';
        foreach (['kerang dara' => 'Kerang dara', 'kepiting' => 'Kepiting', 'lobster' => 'Lobster', 'gurame' => 'Gurame',
            'bawal' => 'Bawal', 'kakap' => 'Kakap', 'kerapu' => 'Kerapu', 'lele' => 'Lele', 'cumi' => 'Cumi',
            'udang' => 'Udang', 'ayam' => 'Ayam', 'bebek' => 'Bebek'] as $word => $label) {
            if (str_contains($n, $word)) {
                $base = $label;
                break;
            }
        }
        $fresh = in_array($base, ['Ayam', 'Bebek', 'Lele'], true) ? $base : "{$base} segar";

        return match (true) {
            str_contains($n, 'crispy') && str_contains($n, 'padang') => "{$fresh} digoreng tepung hingga renyah, disiram saus Padang yang gurih, pedas, dan kaya rempah.",
            str_contains($n, 'crispy') && str_contains($n, 'lada hitam') => "{$fresh} digoreng tepung hingga renyah, dibalut saus lada hitam yang gurih dan hangat di lidah.",
            str_contains($n, 'crispy') => "{$fresh} digoreng tepung hingga renyah dan gurih, nikmat dimakan selagi hangat.",
            str_contains($n, 'bakar') => "{$fresh} dibakar dengan bumbu khas yang meresap hingga ke dalam, beraroma harum dan nikmat disantap bersama nasi hangat.",
            str_contains($n, 'goreng kremes') => "{$fresh} digoreng renyah dengan taburan kremes gurih, nikmat bersama nasi hangat dan sambal.",
            str_contains($n, 'padang') => "{$fresh} dimasak dengan saus Padang yang gurih, pedas, dan kaya rempah.",
            str_contains($n, 'asam manis') => "{$fresh} dimasak dengan saus asam manis pedas yang segar dan menggugah selera.",
            str_contains($n, 'tiram') => "{$fresh} dimasak dengan saus tiram yang gurih dan sedikit manis.",
            str_contains($n, 'lada hitam') => "{$fresh} dimasak dengan saus lada hitam yang gurih dan hangat di lidah.",
            default => "{$fresh} dengan bumbu khas Seafood Muphus yang gurih dan kaya rasa.",
        };
    }
}