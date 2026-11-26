<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

/**
 * Mengatur urutan cabang: Jawa Timur, lalu Jawa Tengah, lalu DI Yogyakarta, lalu Jawa Barat.
 * Hanya kolom sort_order yang diubah. Cabang yang namanya tidak ada di daftar ini ditaruh paling akhir
 * dan namanya dicetak di terminal.
 *
 * Jalankan: php artisan db:seed --class=BranchOrderSeeder
 */
class BranchOrderSeeder extends Seeder
{
    /** Nama setelah awalan "Seafood Muphus ", berurutan. */
    private const ORDER = [
        'Jawa Timur' => [
            'Babat', 'Lamongan', 'Gresik', 'Unesa', 'Surabaya Benowo', 'Mojosari',
            'Probolinggo', 'Kraksaan', 'Malang Singosari', 'Malang Kedung Kandang',
            'Jombang', 'Jember', 'Kediri', 'Madiun', 'Ponorogo', 'Tulungagung',
        ],
        'Jawa Tengah' => [
            'Solo Sumber', 'Solo Baru', 'Solo Laweyan', 'Solo Kartasura', 'Karanganyar',
            'Klaten', 'Sragen', 'Boyolali', 'Kudus Peganjaran', 'Salatiga', 'Ungaran',
            'Pendurungan', 'Tegal',
        ],
        'DI Yogyakarta' => ['Yogyakarta'],
        'Jawa Barat' => ['Cirebon Sumber', 'Cirebon Sunyaragi', 'Subang', 'Purwakarta'],
    ];

    public function run(): void
    {
        $position = 0;
        $seen = [];

        foreach (self::ORDER as $province => $names) {
            foreach ($names as $name) {
                $full = 'Seafood Muphus ' . $name;
                $position++;
                $n = Branch::where('name', $full)->update(['sort_order' => $position]);

                if ($n === 0) {
                    $this->command?->warn("Tidak ditemukan di database: {$full} ({$province})");
                    $position--;
                    continue;
                }

                $seen[] = $full;
            }
        }

        foreach (Branch::whereNotIn('name', $seen)->orderBy('id')->get() as $branch) {
            $position++;
            $branch->update(['sort_order' => $position]);
            $this->command?->warn("Tidak ada di daftar urutan, ditaruh di akhir: {$branch->name}");
        }

        $this->command?->info("Urutan {$position} cabang sudah diatur.");
    }
}
