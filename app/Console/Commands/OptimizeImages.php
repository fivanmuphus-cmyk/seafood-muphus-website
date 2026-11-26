<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Branch;
use App\Models\JobOpening;
use App\Models\MenuItem;
use App\Models\PageBanner;
use App\Support\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize {--dry-run : Hanya tampilkan yang akan diubah}';

    protected $description = 'Ubah foto lama (JPG/PNG) menjadi WebP dan buat salinan kecil untuk HP';

    public function handle(): int
    {
        if (! ImageOptimizer::supported()) {
            $this->error('Ekstensi GD dengan dukungan WebP tidak aktif. Aktifkan "extension=gd" di php.ini.');

            return self::FAILURE;
        }

        // [model, kolom, lebar maksimal, kualitas]
        $targets = [
            [MenuItem::class, 'image_path', 1200, 82],
            [Branch::class, 'photo_path', 1600, 82],
            [JobOpening::class, 'brochure_path', 1500, 85],
            [Article::class, 'cover_path', 1600, 82],
            [PageBanner::class, 'image_path', 2400, 60],
        ];

        $disk = Storage::disk('public');
        $converted = 0;
        $variants = 0;
        $savedBytes = 0;

        foreach ($targets as [$model, $column, $width, $quality]) {
            foreach ($model::query()->whereNotNull($column)->get() as $row) {
                $old = (string) $row->{$column};

                if ($old === '') {
                    continue;
                }

                // Sudah WebP: cukup pastikan salinan kecilnya ada.
                if (str_ends_with(strtolower($old), '.webp')) {
                    if ($this->option('dry-run')) {
                        $this->line("Cek salinan kecil: {$old}");
                        continue;
                    }

                    $made = ImageOptimizer::makeVariants($old, $quality);
                    $variants += $made;
                    if ($made > 0) {
                        $this->info("{$old}: {$made} salinan kecil dibuat");
                    }

                    continue;
                }

                if ($this->option('dry-run')) {
                    $this->line("Akan diubah: {$old}");

                    continue;
                }

                $before = $disk->exists($old) ? $disk->size($old) : 0;
                $new = ImageOptimizer::convertStored($old, $width, $quality);

                if ($new === null) {
                    $this->warn("Dilewati: {$old}");

                    continue;
                }

                $row->{$column} = $new;
                $row->saveQuietly();

                $after = $disk->size($new);
                $savedBytes += max(0, $before - $after);
                $converted++;
                $this->info(sprintf('%s → %s (%s → %s)', $old, $new, self::kb($before), self::kb($after)));
            }
        }

        $this->newLine();
        $this->info($this->option('dry-run')
            ? 'Selesai (simulasi, tidak ada yang diubah).'
            : "Selesai. {$converted} foto diubah ke WebP (hemat " . self::kb($savedBytes) . "), {$variants} salinan kecil tambahan dibuat.");

        return self::SUCCESS;
    }

    private static function kb(int $bytes): string
    {
        return number_format($bytes / 1024, 0, ',', '.') . ' KB';
    }
}