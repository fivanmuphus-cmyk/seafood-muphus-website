<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Throwable;

/**
 * Pembaca pengaturan situs. Nilai dari database dipakai bila ada; bila kosong,
 * dipakai teks bawaan di bawah ini (atau nilai di config/muphus.php untuk kelompok "general").
 *
 * Judul boleh memuat *kata* untuk ditampilkan miring berwarna hijau (lihat em()).
 */
class Site
{
    private const CACHE_KEY = 'site_settings.v1';

    private static ?array $memo = null;

    /** Kolom pengaturan umum => jalur di config('muphus.*'). */
    public const GENERAL_MAP = [
        'name' => 'name',
        'tagline' => 'tagline',
        'whatsapp' => 'contact.whatsapp',
        'whatsapp_display' => 'contact.whatsapp_display',
        'partnership_whatsapp' => 'contact.partnership_whatsapp',
        'partnership_whatsapp_display' => 'contact.partnership_whatsapp_display',
        'hr_whatsapp' => 'contact.hr_whatsapp',
        'hr_whatsapp_display' => 'contact.hr_whatsapp_display',
        'email' => 'contact.email',
        'hr_email' => 'contact.hr_email',
        'address' => 'contact.address',
        'hr_instagram' => 'contact.hr_instagram',
        'instagram' => 'social.instagram',
        'tiktok' => 'social.tiktok',
        'facebook' => 'social.facebook',
        'analytics_id' => 'analytics.id',
        'threads' => 'social.threads',
        'youtube' => 'social.youtube',
        'target_outlets' => 'stats.target_outlets',
        'founded' => 'stats.founded',
        'min_land' => 'stats.min_land',
        'target_year' => 'stats.target_year',
    ];

    /** Bawaan yang diambil dari config/muphus.php: kunci pengaturan => jalur config. */
    public const CONFIG_DEFAULTS = [
        'partnership.land_area' => 'land_spec.area',
        'partnership.land_width' => 'land_spec.width',
        'partnership.land_depth' => 'land_spec.depth',
        'partnership.land_road' => 'land_spec.road',
        'partnership.land_status' => 'land_spec.status',
        'contact.hours' => 'service_hours',
    ];

    /** Teks bawaan per kelompok. Sama persis dengan tampilan sebelum bisa diedit. */
    public const DEFAULTS = [
        'home' => [
            'hero_eyebrow' => 'Rajanya Seafood Tenda',
            'hero_title' => 'Seafood muphus',
            'hero_subtitle' => 'Segar dari pilihan terbaik, diolah dengan bumbu yang harum dan kaya rasa. Seafood tenda favorit yang hadir di banyak kota, dekat dari rumah Anda.',
            'hero_tagline' => 'seafood segar *andalan* keluarga',
            'hero_cta1_label' => 'Lihat Menu',
            'hero_cta1_url' => '/menu',
            'hero_cta2_label' => 'Cari Cabang',
            'hero_cta2_url' => '/cabang',

            'about_eyebrow' => 'Tentang Muphus',
            'about_title' => 'Tempat makan seafood yang *hangat*, untuk semua keluarga.',
            'about_text' => 'Muphus berawal dari satu keyakinan sederhana: seafood yang enak harus segar, dimasak dengan benar, dan bisa dinikmati siapa saja. Kini kami hadir di banyak kota, dengan rasa yang sama di setiap cabang.',
            'about_link_label' => 'Kenali Muphus lebih dekat',
            'about_points' => [
                ['title' => 'Bahan segar pilihan', 'text' => 'Seafood dipilih dengan teliti dan diolah di hari yang sama.'],
                ['title' => 'Bumbu kaya rasa', 'text' => 'Racikan bumbu yang meresap, konsisten di setiap cabang.'],
                ['title' => 'Ramah untuk keluarga', 'text' => 'Porsi dan harga yang pas untuk makan bersama.'],
                ['title' => 'Dekat dari rumah', 'text' => 'Cabang tersebar di banyak kota, mudah dijangkau.'],
            ],

            'menu_eyebrow' => 'Menu populer',
            'menu_title' => 'Pilihan *favorit* pelanggan kami.',
            'menu_text' => 'Hidangan yang paling sering dipesan di meja keluarga. Mulai dari kepiting hingga cumi, semuanya dimasak hangat dengan bumbu pilihan.',
            'menu_link_label' => 'Lihat semua menu',

            'branches_eyebrow' => 'Cabang kami',
            'branches_title' => 'Temukan Seafood Muphus *terdekat* dari Anda.',
            'branches_text' => 'Buka setiap hari untuk makan siang dan malam. Pilih cabang, lihat jam buka, lalu langsung buka petunjuk arahnya.',
            'branches_link_label' => 'Lihat semua cabang',

            'articles_eyebrow' => 'Artikel',
            'articles_title' => 'Cerita dan tips *seputar seafood.*',
            'articles_text' => 'Panduan memilih bahan, ide masakan, dan kabar terbaru dari Muphus.',
            'articles_link_label' => 'Lihat semua artikel',

            'careers_eyebrow' => 'Karir',
            'careers_title' => 'Tumbuh bersama *Seafood Muphus.*',
            'careers_text' => 'Kami mencari orang-orang yang suka melayani dan ingin berkembang. Bergabunglah dengan tim di berbagai cabang kami, dari dapur sampai meja pelanggan.',
            'careers_link_label' => 'Lihat semua lowongan',

            'partner_eyebrow' => 'Kemitraan',
            'partner_title' => 'Buka Seafood Muphus *di kota Anda.*',
            'partner_text' => 'Muphus terus berkembang dan membuka peluang kemitraan di berbagai wilayah. Jika Anda memiliki lahan yang sesuai, mari bertumbuh bersama kami.',
            'partner_stat_1' => 'cabang beroperasi',
            'partner_stat_2' => 'target outlet di {tahun}',
            'partner_stat_3' => 'luas lahan minimal',
            'partner_wa_label' => 'Hubungi via WhatsApp',
            'partner_wa_message' => 'Halo, saya tertarik dengan kemitraan Seafood Muphus.',
            'partner_link_label' => 'Lihat syarat kemitraan',
            'partner_image' => null,
        ],
    ];

    /** Semua pengaturan tersimpan: [group => [kunci => nilai]]. */
    public static function all(): array
    {
        if (self::$memo !== null) {
            return self::$memo;
        }

        try {
            return self::$memo = Cache::rememberForever(self::CACHE_KEY, fn (): array => SiteSetting::query()
                ->get()
                ->mapWithKeys(fn (SiteSetting $row) => [$row->group => (array) ($row->data ?? [])])
                ->all());
        } catch (Throwable) {
            // Tabel belum dimigrasi atau cache bermasalah: pakai teks bawaan.
            return self::$memo = [];
        }
    }

    public static function flush(): void
    {
        self::$memo = null;

        try {
            Cache::forget(self::CACHE_KEY);
        } catch (Throwable) {
        }
    }

    /** Teks bawaan untuk kunci "kelompok.nama". */
    public static function default(string $key): mixed
    {
        [$group, $name] = array_pad(explode('.', $key, 2), 2, '');

        if ($group === 'general') {
            $path = self::GENERAL_MAP[$name] ?? null;
            $value = $path ? config('muphus.' . $path) : null;

            return is_scalar($value) ? $value : null;
        }

        if (isset(self::CONFIG_DEFAULTS[$key])) {
            $value = config('muphus.' . self::CONFIG_DEFAULTS[$key]);

            return is_scalar($value) ? $value : null;
        }

        return self::table()[$group][$name] ?? null;
    }

    /** Semua bawaan satu kelompok. */
    public static function defaults(string $group): array
    {
        if ($group === 'general') {
            $out = [];
            foreach (array_keys(self::GENERAL_MAP) as $name) {
                $out[$name] = self::default("general.{$name}");
            }

            return $out;
        }

        $out = self::table()[$group] ?? [];

        foreach (array_keys(self::CONFIG_DEFAULTS) as $key) {
            if (str_starts_with($key, $group . '.')) {
                $out[substr($key, strlen($group) + 1)] = self::default($key);
            }
        }

        return $out;
    }

    private static function table(): array
    {
        return self::DEFAULTS + SiteDefaults::ALL;
    }

    /** Daftar (repeater) sebagai baris berurutan [kolom1, kolom2, ...] agar mudah di-foreach. */
    public static function rows(string $key, array $fields): array
    {
        return array_map(
            fn (array $row) => array_map(fn (string $f) => (string) ($row[$f] ?? ''), $fields),
            self::list($key, in_array('title', $fields, true) ? 'title' : $fields[0])
        );
    }

    /** Data tersimpan digabung bawaan: yang kosong diisi bawaan. */
    public static function withDefaults(string $group, ?array $saved): array
    {
        $out = self::defaults($group);

        foreach ((array) $saved as $name => $value) {
            if ($value !== null && $value !== '' && $value !== []) {
                $out[$name] = $value;
            }
        }

        return $out;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        [$group, $name] = array_pad(explode('.', $key, 2), 2, '');
        $value = self::all()[$group][$name] ?? null;

        if ($value === null || $value === '' || $value === []) {
            return $default ?? self::default($key);
        }

        return $value;
    }

    public static function text(string $key, ?string $default = null): string
    {
        $value = self::get($key, $default);

        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * Teks aman untuk judul: semua HTML di-escape lebih dulu, baru *kata* diubah menjadi <em>.
     */
    public static function em(string $key, string $class = '', array $vars = []): HtmlString
    {
        $safe = e(self::fill(self::text($key), $vars));
        $attr = $class !== '' ? ' class="' . e($class) . '"' : '';

        return new HtmlString(preg_replace('/\*([^*\n]+)\*/u', "<em{$attr}>$1</em>", $safe));
    }

    /** Mengganti {kunci} di dalam teks dengan nilai dari $vars. */
    public static function fill(string $text, array $vars): string
    {
        return str_replace(
            array_map(fn ($k) => '{' . $k . '}', array_keys($vars)),
            array_map('strval', array_values($vars)),
            $text
        );
    }

    /** Tautan aman: hanya "/jalur" atau "https://...". Selain itu menjadi "#". */
    public static function url(string $key): string
    {
        $value = self::text($key);

        return preg_match('#^(/(?!/)|https://)#', $value) ? $value : '#';
    }

    /** Daftar (repeater) dengan baris yang tidak lengkap dibuang. */
    public static function list(string $key, string $required = 'title'): array
    {
        $rows = self::get($key, []);

        return collect(is_array($rows) ? $rows : [])
            ->filter(fn ($row) => is_array($row) && filled($row[$required] ?? null))
            ->values()
            ->all();
    }

    /**
     * Semua akun media sosial yang terisi, berurutan: akun tetap (Instagram, Facebook, TikTok, Threads, YouTube),
     * lalu "Media sosial lainnya" dari admin. Hanya tautan https yang dipakai.
     *
     * @return array<int, array{key: ?string, label: string, url: string}>
     */
    public static function socialLinks(): array
    {
        $fixed = ['instagram' => 'Instagram', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'threads' => 'Threads', 'youtube' => 'YouTube'];
        $links = [];

        foreach ($fixed as $key => $label) {
            $url = config('muphus.social.' . $key);

            if (is_string($url) && str_starts_with(trim($url), 'https://')) {
                $links[] = ['key' => $key, 'label' => $label, 'url' => trim($url)];
            }
        }

        foreach ((array) (self::all()['general']['social_extra'] ?? []) as $row) {
            $label = is_array($row) ? trim((string) ($row['label'] ?? '')) : '';
            $url = is_array($row) ? trim((string) ($row['url'] ?? '')) : '';

            if ($label !== '' && str_starts_with($url, 'https://')) {
                $links[] = ['key' => null, 'label' => $label, 'url' => $url];
            }
        }

        return $links;
    }

    /** Menimpa config('muphus.*') dengan isi pengaturan umum yang terisi. */
    public static function applyToConfig(): void
    {
        $general = self::all()['general'] ?? [];

        foreach (self::GENERAL_MAP as $name => $path) {
            $value = $general[$name] ?? null;

            if (is_scalar($value) && trim((string) $value) !== '') {
                config(['muphus.' . $path => is_string($value) ? trim($value) : $value]);
            }
        }
    }
}