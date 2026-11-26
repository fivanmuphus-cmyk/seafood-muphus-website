<?php

namespace Database\Seeders;

use App\Models\JobOpening;
use Illuminate\Database\Seeder;

/**
 * Menghapus SEMUA lowongan (contoh maupun lainnya). Artikel, menu, cabang, dan FAQ tidak disentuh.
 * Halaman Karir akan menampilkan pesan "Belum ada lowongan terbuka" sampai Anda menambah lowongan di admin.
 * Penghapusan tidak bisa dibatalkan; daftar yang dihapus dicetak di terminal.
 *
 * Jalankan: php artisan db:seed --class=ClearJobOpeningsSeeder
 */
class ClearJobOpeningsSeeder extends Seeder
{
    public function run(): void
    {
        $titles = JobOpening::query()->orderBy('id')->pluck('position');

        foreach ($titles as $title) {
            $this->command?->line(' - ' . $title);
        }

        $n = JobOpening::query()->delete();

        $this->command?->info("{$n} lowongan dihapus. Tambah lowongan baru lewat admin: Lowongan > Buat.");
    }
}
