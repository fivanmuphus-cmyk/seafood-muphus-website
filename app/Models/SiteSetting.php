<?php

namespace App\Models;

use App\Support\Site;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    /** Semua kelompok pengaturan: kunci => nama tampilan. */
    public const GROUPS = [
        'general' => 'Pengaturan Umum',
        'home' => 'Beranda',
        'about' => 'Tentang',
        'menu' => 'Menu',
        'branches' => 'Cabang',
        'articles' => 'Artikel',
        'careers' => 'Karir',
        'partnership' => 'Kemitraan',
        'contact' => 'Kontak',
    ];

    /** Kelompok yang formulirnya sudah tersedia (tambah di sini saat halaman baru selesai). */
    public const IMPLEMENTED = ['general', 'home', 'partnership', 'careers', 'contact', 'about', 'menu', 'branches', 'articles'];

    /** Kelompok yang boleh diubah admin biasa. Selain ini khusus superadmin. */
    public const ADMIN_GROUPS = ['home', 'menu', 'branches', 'articles', 'careers', 'contact'];

    protected $fillable = ['group', 'data'];

    protected function casts(): array
    {
        return ['data' => 'array'];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Site::flush());
        static::deleted(fn () => Site::flush());
    }

    /** Membuat baris kosong untuk setiap kelompok yang formulirnya sudah ada. */
    public static function ensureRows(): void
    {
        foreach (self::IMPLEMENTED as $group) {
            self::query()->firstOrCreate(['group' => $group]);
        }
    }

    /**
     * Superadmin boleh mengubah semua pengaturan. Admin hanya kelompok di ADMIN_GROUPS.
     */
    public static function userCanEdit(string $group, $user): bool
    {
        if (! $user) {
            return false;
        }

        if (method_exists($user, 'isSuperadmin') && $user->isSuperadmin()) {
            return true;
        }

        return in_array($group, self::ADMIN_GROUPS, true);
    }
}
