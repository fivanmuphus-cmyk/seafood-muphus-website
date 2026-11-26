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
 * Menghapus semua data contoh yang dibuat DummyContentSeeder.
 * Yang dihapus HANYA yang bertanda: sort_order >= 900 (artikel: slug "contoh-%").
 * Data yang Anda isi sendiri lewat admin tidak tersentuh.
 *
 * Jalankan: php artisan db:seed --class=ClearDummyContentSeeder
 */
class ClearDummyContentSeeder extends Seeder
{
    public function run(): void
    {
        $counts = [
            'Menu' => MenuItem::where('sort_order', '>=', 900)->delete(),
            'Cabang' => Branch::where('sort_order', '>=', 900)->delete(),
            'Lowongan' => JobOpening::where('sort_order', '>=', 900)->delete(),
            'FAQ' => Faq::where('sort_order', '>=', 900)->delete(),
            'Artikel' => Article::where('slug', 'like', 'contoh-%')->delete(),
        ];

        Storage::disk('public')->deleteDirectory('dummy');

        foreach ($counts as $label => $n) {
            $this->command?->info("{$label}: {$n} data dihapus");
        }
        $this->command?->info('Gambar contoh di storage/app/public/dummy dihapus.');
    }
}