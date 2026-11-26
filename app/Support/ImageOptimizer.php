<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Mengubah foto unggahan menjadi WebP yang ringan, dan membuat salinan kecil (480 dan 960 px)
 * supaya HP tidak perlu mengunduh foto besar. Memakai ekstensi GD.
 * Bila GD tidak mendukung WebP atau formatnya tidak dikenal, foto disimpan apa adanya.
 *
 * Pemakaian di FileUpload Filament:
 *   ->saveUploadedFileUsing(\App\Support\ImageOptimizer::saver('menu', 1200))
 */
class ImageOptimizer
{
    /** Lebar salinan kecil untuk srcset (hanya dibuat bila fotonya lebih lebar). */
    public const VARIANT_WIDTHS = [480, 960];

    public static function supported(): bool
    {
        return function_exists('imagewebp') && function_exists('imagecreatetruecolor');
    }

    /** Membuat fungsi penyimpan untuk ->saveUploadedFileUsing(...). */
    public static function saver(string $directory, int $maxWidth = 1600, int $quality = 82): Closure
    {
        return fn ($file): string => self::store($file, $directory, $maxWidth, $quality);
    }

    /** Menyimpan berkas unggahan sebagai WebP di disk "public" beserta salinan kecilnya. */
    public static function store($file, string $directory, int $maxWidth = 1600, int $quality = 82): string
    {
        $directory = trim($directory, '/');
        $data = self::encode($file->getRealPath(), (string) $file->getMimeType(), $maxWidth, $quality);

        if ($data === null) {
            return $file->storePubliclyAs($directory, Str::uuid() . '.' . $file->extension(), 'public');
        }

        $path = $directory . '/' . Str::uuid() . '.webp';
        Storage::disk('public')->put($path, $data, 'public');
        self::makeVariants($path, $quality);

        return $path;
    }

    /**
     * Mengubah berkas yang SUDAH tersimpan (JPG/PNG) menjadi WebP, lalu menghapus yang lama.
     * Mengembalikan jalur baru, atau null bila tidak ada yang diubah.
     */
    public static function convertStored(string $path, int $maxWidth = 1600, int $quality = 82): ?string
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($path) || str_ends_with(strtolower($path), '.webp')) {
            return null;
        }

        $absolute = $disk->path($path);
        $data = self::encode($absolute, (string) (mime_content_type($absolute) ?: ''), $maxWidth, $quality);

        if ($data === null) {
            return null;
        }

        $dir = dirname($path);
        $new = ($dir === '.' ? '' : $dir . '/') . pathinfo($path, PATHINFO_FILENAME) . '.webp';

        $disk->put($new, $data, 'public');
        $disk->delete($path);
        self::makeVariants($new, $quality);

        return $new;
    }

    public static function variantPath(string $path, int $width): string
    {
        return preg_replace('/\.webp$/i', "-{$width}.webp", $path);
    }

    /** Membuat salinan kecil dari foto WebP yang sudah tersimpan. Mengembalikan jumlah yang dibuat. */
    public static function makeVariants(string $path, int $quality = 80): int
    {
        $disk = Storage::disk('public');

        if (! self::supported() || ! str_ends_with(strtolower($path), '.webp') || ! $disk->exists($path)) {
            return 0;
        }

        $absolute = $disk->path($path);
        $size = @getimagesize($absolute);
        $width = is_array($size) ? (int) $size[0] : 0;
        $made = 0;

        foreach (self::VARIANT_WIDTHS as $target) {
            $variant = self::variantPath($path, $target);

            if ($width <= $target || $disk->exists($variant)) {
                continue;
            }

            $data = self::encode($absolute, 'image/webp', $target, $quality);

            if ($data !== null) {
                $disk->put($variant, $data, 'public');
                $made++;
            }
        }

        Cache::forget('srcset:' . $path);

        return $made;
    }

    /** Teks srcset untuk <img>, atau null bila belum ada salinan kecil. */
    public static function srcset(?string $path): ?string
    {
        if (! $path || ! str_ends_with(strtolower($path), '.webp')) {
            return null;
        }

        $value = Cache::remember('srcset:' . $path, now()->addHour(), function () use ($path): string {
            $disk = Storage::disk('public');
            $parts = [];

            foreach (self::VARIANT_WIDTHS as $width) {
                $variant = self::variantPath($path, $width);

                if ($disk->exists($variant)) {
                    $parts[] = '/storage/' . $variant . " {$width}w";
                }
            }

            if (! $parts) {
                return '';
            }

            $size = @getimagesize($disk->path($path));

            if (is_array($size)) {
                $parts[] = '/storage/' . $path . " {$size[0]}w";
            }

            return implode(', ', $parts);
        });

        return $value !== '' ? $value : null;
    }

    private static function encode(string $file, string $mime, int $maxWidth, int $quality): ?string
    {
        if (! self::supported()) {
            return null;
        }

        // Pengaman memori: foto dengan piksel terlalu banyak tidak diolah (disimpan apa adanya).
        $dimensions = @getimagesize($file);
        $limit = self::memoryLimitBytes();

        if (is_array($dimensions) && $limit > 0 && ($dimensions[0] * $dimensions[1] * 5) > ($limit * 0.75 - memory_get_usage())) {
            return null;
        }

        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($file),
            'image/png' => @imagecreatefrompng($file),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file) : false,
            default => false,
        };

        if (! $image) {
            return null;
        }

        // Foto dari HP sering "miring" karena orientasi disimpan di metadata.
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($file);
            $orientation = is_array($exif) ? ($exif['Orientation'] ?? 1) : 1;
            $rotated = match ($orientation) {
                3 => imagerotate($image, 180, 0),
                6 => imagerotate($image, -90, 0),
                8 => imagerotate($image, 90, 0),
                default => $image,
            };
            if ($rotated) {
                $image = $rotated;
            }
        }

        if (imagesx($image) > $maxWidth) {
            $scaled = imagescale($image, $maxWidth, -1, IMG_BICUBIC);
            if ($scaled) {
                $image = $scaled;
            }
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        ob_start();
        $ok = imagewebp($image, null, $quality);
        $data = ob_get_clean();

        return ($ok && is_string($data) && $data !== '') ? $data : null;
    }

    private static function memoryLimitBytes(): int
    {
        $value = trim((string) ini_get('memory_limit'));

        if ($value === '' || $value === '-1') {
            return 0; // tanpa batas
        }

        $number = (int) $value;

        return match (strtolower(substr($value, -1))) {
            'g' => $number * 1024 ** 3,
            'm' => $number * 1024 ** 2,
            'k' => $number * 1024,
            default => $number,
        };
    }
}