<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Hanya MEMBACA. Menampilkan data yang tampak seperti data uji sebelum situs tayang. Tidak mengubah apa pun.
 *
 * Jalankan: php artisan muphus:periksa-data
 */
class PeriksaData extends Command
{
    protected $signature = 'muphus:periksa-data';

    protected $description = 'Daftar data yang tampak seperti data uji (hanya membaca, tidak mengubah apa pun)';

    public function handle(): int
    {
        $this->show('Akun admin (users)', 'users', ['id', 'name', 'email', 'created_at']);
        $this->show('Pesan masuk (contact_messages), 10 terbaru', 'contact_messages', ['id', 'name', 'email', 'topic', 'created_at'], 10);
        $this->show('Artikel', 'articles', ['id', 'title', 'slug', 'published_at']);
        $this->show('Lowongan', 'job_openings', ['id', 'title', 'sort_order']);
        $this->show('Cabang', 'branches', ['id', 'name', 'city', 'sort_order']);

        foreach (['menu_items' => 'Menu', 'faqs' => 'FAQ'] as $table => $label) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $total = DB::table($table)->count();
            $dummy = Schema::hasColumn($table, 'sort_order') ? DB::table($table)->where('sort_order', '>=', 900)->count() : 0;
            $this->line("{$label}: {$total} data, {$dummy} bertanda contoh (sort_order >= 900)");
        }

        $this->line('Folder gambar contoh (storage/app/public/dummy): ' . (Storage::disk('public')->exists('dummy') ? 'ADA' : 'tidak ada'));
        $this->newLine();
        $this->info('Selesai. Tidak ada data yang diubah.');

        return self::SUCCESS;
    }

    private function show(string $title, string $table, array $columns, int $limit = 50): void
    {
        $this->newLine();

        if (! Schema::hasTable($table)) {
            $this->warn("{$title}: tabel {$table} tidak ada.");

            return;
        }

        $columns = array_values(array_filter($columns, fn ($c) => Schema::hasColumn($table, $c)));
        $total = DB::table($table)->count();
        $this->info("{$title}: {$total} data");

        if ($total === 0) {
            return;
        }

        $order = in_array('id', $columns, true) ? 'id' : $columns[0];
        $rows = DB::table($table)->select($columns)->orderByDesc($order)->limit($limit)->get()
            ->map(fn ($r) => array_map(fn ($v) => mb_strimwidth((string) $v, 0, 45, '…'), (array) $r))
            ->all();

        $this->table($columns, $rows);
    }
}
