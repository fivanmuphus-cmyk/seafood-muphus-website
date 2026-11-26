<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Mencari foto di storage/app/public yang TIDAK dipakai lagi oleh data mana pun
 * (sisa foto lama setelah diganti di admin), lalu memindahkannya ke storage/app/sampah-foto.
 *
 * Jalankan:
 *   php artisan muphus:bersihkan-foto --cek      (hanya menampilkan daftar, tidak mengubah apa pun)
 *   php artisan muphus:bersihkan-foto            (memindahkan ke storage/app/sampah-foto setelah konfirmasi)
 *   php artisan muphus:bersihkan-foto --hapus    (menghapus permanen setelah konfirmasi)
 */
class BersihkanFoto extends Command
{
    protected $signature = 'muphus:bersihkan-foto {--cek : Hanya tampilkan daftar} {--hapus : Hapus permanen, bukan dipindahkan} {--yes : Lewati pertanyaan konfirmasi}';

    protected $description = 'Pindahkan (atau hapus) foto di storage yang tidak dipakai lagi oleh data mana pun';

    /** Tabel yang tidak mungkin menyimpan jalur foto. */
    private const SKIP = ['migrations', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'password_reset_tokens'];

    public function handle(): int
    {
        $disk = Storage::disk('public');

        // 1) Kumpulkan seluruh isi database menjadi satu teks besar (jalur foto bisa ada di kolom biasa atau di JSON).
        $haystack = '';
        foreach (Schema::getTableListing() as $table) {
            $name = str_contains($table, '.') ? substr($table, strrpos($table, '.') + 1) : $table;
            if (in_array($name, self::SKIP, true)) {
                continue;
            }
            DB::table($name)->orderBy(DB::raw('1'))->chunk(200, function ($rows) use (&$haystack) {
                foreach ($rows as $row) {
                    $haystack .= json_encode($row, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
                }
            });
        }
        // Jalur di dalam JSON bisa tersimpan dengan garis miring ter-escape sekali atau dua kali
        // (banners\/x.webp atau banners\\/x.webp). Samakan semuanya menjadi banners/x.webp.
        $haystack = preg_replace('#\\\\+/#', '/', $haystack);

        // 2) Periksa setiap berkas di disk "public".
        $unused = [];
        $used = 0;
        $bytes = 0;

        foreach ($disk->allFiles() as $path) {
            if (basename($path) === '.gitignore') {
                continue;
            }

            // x-480.webp dan x-960.webp ikut induknya (x.webp).
            $induk = preg_replace('/-(480|960)(\.[A-Za-z0-9]+)$/', '$2', $path);

            if (str_contains($haystack, $induk)) {
                $used++;
                continue;
            }

            $unused[] = $path;
            $bytes += $disk->size($path);
        }

        $mb = round($bytes / 1048576, 1);
        $this->info("Foto dipakai: {$used} berkas. Tidak dipakai: " . count($unused) . " berkas ({$mb} MB).");

        if (! $unused) {
            return self::SUCCESS;
        }

        foreach ($unused as $path) {
            $this->line('  - ' . $path);
        }

        if ($this->option('cek')) {
            $this->newLine();
            $this->comment('Mode --cek: tidak ada yang diubah.');

            return self::SUCCESS;
        }

        $hapus = (bool) $this->option('hapus');
        $pertanyaan = $hapus
            ? 'Hapus PERMANEN berkas di atas?'
            : 'Pindahkan berkas di atas ke storage/app/sampah-foto?';

        if (! $this->option('yes') && ! $this->confirm($pertanyaan, false)) {
            $this->comment('Dibatalkan. Tidak ada yang diubah.');

            return self::SUCCESS;
        }

        foreach ($unused as $path) {
            if ($hapus) {
                $disk->delete($path);
                continue;
            }
            $tujuan = storage_path('app/sampah-foto/' . $path);
            File::ensureDirectoryExists(dirname($tujuan));
            File::move($disk->path($path), $tujuan);
        }

        // Rapikan folder yang kosong (kecuali folder induk).
        foreach ($disk->allDirectories() as $dir) {
            if (! $disk->allFiles($dir) && ! $disk->allDirectories($dir)) {
                $disk->deleteDirectory($dir);
            }
        }

        $this->info($hapus
            ? 'Selesai. Berkas dihapus permanen.'
            : 'Selesai. Berkas dipindahkan ke storage/app/sampah-foto (aman dihapus nanti setelah situs dicek).');

        return self::SUCCESS;
    }
}
