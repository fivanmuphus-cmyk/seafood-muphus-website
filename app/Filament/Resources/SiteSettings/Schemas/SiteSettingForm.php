<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use App\Models\SiteSetting;
use App\Support\ImageOptimizer;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class SiteSettingForm
{
    private const LINK_RULE = '/^(\/(?!\/)|https:\/\/)/';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(function (?Model $record): array {
            return match ($record?->group) {
                'general' => self::general(),
                'home' => self::home(),
                'partnership' => self::partnership(),
                'careers' => self::careers(),
                'contact' => self::contact(),
                'about' => self::about(),
                'menu' => self::menu(),
                'branches' => self::branches(),
                'articles' => self::articles(),
                default => [],
            };
        });
    }

    private static function title(string $key, string $label = 'Judul'): TextInput
    {
        return TextInput::make("data.{$key}")->label($label)->maxLength(160)
            ->helperText('Bungkus satu kata dengan bintang untuk dimiringkan berwarna hijau, misalnya: Pilihan *favorit* pelanggan kami.');
    }

    private static function link(string $key, string $label): TextInput
    {
        return TextInput::make("data.{$key}")->label($label)->maxLength(200)
            ->regex(self::LINK_RULE)->validationMessages(['regex' => 'Tautan harus diawali "/" (halaman di situs ini) atau "https://".']);
    }

    /** Bagian judul + teks + tautan untuk satu seksi Beranda. */
    private static function block(string $heading, string $prefix, bool $text = true): Section
    {
        $fields = [
            TextInput::make("data.{$prefix}_eyebrow")->label('Label kecil di atas judul')->maxLength(60),
            self::title("{$prefix}_title"),
        ];

        if ($text) {
            $fields[] = Textarea::make("data.{$prefix}_text")->label('Paragraf')->rows(3)->maxLength(400);
        }

        $fields[] = TextInput::make("data.{$prefix}_link_label")->label('Tulisan tautan')->maxLength(60);

        return Section::make($heading)->collapsible()->collapsed()->columns(1)->schema($fields);
    }

    private static function home(): array
    {
        return [
            Section::make('Hero (bagian paling atas)')->collapsible()->columns(2)->schema([
                TextInput::make('data.hero_eyebrow')->label('Label kecil')->maxLength(60)->columnSpanFull(),
                TextInput::make('data.hero_title')->label('Judul besar')->maxLength(80)->columnSpanFull(),
                Textarea::make('data.hero_subtitle')->label('Subjudul')->rows(3)->maxLength(300)->columnSpanFull(),
                self::title('hero_tagline', 'Tagline')->columnSpanFull(),
                TextInput::make('data.hero_cta1_label')->label('Tombol 1: tulisan')->maxLength(40),
                self::link('hero_cta1_url', 'Tombol 1: tautan'),
                TextInput::make('data.hero_cta2_label')->label('Tombol 2: tulisan')->maxLength(40),
                self::link('hero_cta2_url', 'Tombol 2: tautan'),
            ])->description('Foto hero diganti lewat menu "Banner halaman" (halaman Beranda).'),

            Section::make('Tentang')->collapsible()->collapsed()->columns(1)->schema([
                TextInput::make('data.about_eyebrow')->label('Label kecil di atas judul')->maxLength(60),
                self::title('about_title'),
                Textarea::make('data.about_text')->label('Paragraf')->rows(4)->maxLength(500),
                TextInput::make('data.about_link_label')->label('Tulisan tautan')->maxLength(60),
                Repeater::make('data.about_points')->label('Butir keunggulan')
                    ->schema([
                        TextInput::make('title')->label('Judul')->required()->maxLength(60),
                        Textarea::make('text')->label('Keterangan')->rows(2)->maxLength(200),
                    ])
                    ->minItems(1)->maxItems(6)->reorderable()->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                    ->addActionLabel('Tambah butir'),
            ]),

            self::block('Menu populer', 'menu'),
            self::block('Cabang', 'branches'),
            self::block('Artikel', 'articles'),
            self::block('Karir', 'careers'),

            Section::make('Kemitraan')->collapsible()->collapsed()->columns(1)->schema([
                TextInput::make('data.partner_eyebrow')->label('Label kecil di atas judul')->maxLength(60),
                self::title('partner_title'),
                Textarea::make('data.partner_text')->label('Paragraf')->rows(3)->maxLength(400),
                TextInput::make('data.partner_stat_1')->label('Keterangan angka 1 (jumlah cabang)')->maxLength(40),
                TextInput::make('data.partner_stat_2')->label('Keterangan angka 2 (target outlet)')->maxLength(60)
                    ->helperText('Tulis {tahun} untuk memakai tahun target secara otomatis.'),
                TextInput::make('data.partner_stat_3')->label('Keterangan angka 3 (luas lahan)')->maxLength(40),
                TextInput::make('data.partner_wa_label')->label('Tulisan tombol WhatsApp')->maxLength(40),
                Textarea::make('data.partner_wa_message')->label('Pesan awal WhatsApp')->rows(2)->maxLength(200),
                TextInput::make('data.partner_link_label')->label('Tulisan tautan')->maxLength(60),
                FileUpload::make('data.partner_image')->label('Foto kemitraan')
                    ->disk('public')->directory('site')->image()->maxSize(11264)
                    ->saveUploadedFileUsing(ImageOptimizer::saver('site', 1600, 82))
                    ->helperText('Kosongkan untuk memakai foto bawaan. Foto otomatis dikecilkan ke WebP.'),
            ]),
        ];
    }

    private static function general(): array
    {
        $number = fn (string $key, string $label) => TextInput::make("data.{$key}")->label($label)
            ->regex('/^\d{8,15}$/')->validationMessages(['regex' => 'Hanya angka, diawali kode negara tanpa +, contoh: 6281234567890.'])
            ->helperText('Format: 6281234567890 (tanpa +, spasi, atau tanda hubung).');

        $social = fn (string $key, string $label) => TextInput::make("data.{$key}")->label($label)
            ->url()->maxLength(300)->regex('/^https:\/\//')
            ->validationMessages(['regex' => 'Tautan harus diawali https://']);

        return [
            Section::make('Merek')->columns(1)->schema([
                TextInput::make('data.name')->label('Nama merek')->required()->maxLength(60),
                TextInput::make('data.tagline')->label('Tagline')->maxLength(160),
            ]),
            Section::make('Kontak')->columns(2)->schema([
                $number('whatsapp', 'WhatsApp admin (umum, untuk tautan)'),
                TextInput::make('data.whatsapp_display')->label('WhatsApp admin (tampilan)')->maxLength(30),
                $number('partnership_whatsapp', 'WhatsApp Kemitraan (untuk tautan)'),
                TextInput::make('data.partnership_whatsapp_display')->label('WhatsApp Kemitraan (tampilan)')->maxLength(30),
                $number('hr_whatsapp', 'WhatsApp HRD (untuk tautan)'),
                TextInput::make('data.hr_whatsapp_display')->label('WhatsApp HRD (tampilan)')->maxLength(30),
                TextInput::make('data.email')->label('Email')->email()->maxLength(120),
                TextInput::make('data.hr_email')->label('Email HRD')->email()->maxLength(120),
                Textarea::make('data.address')->label('Alamat kantor')->rows(2)->maxLength(300)->columnSpanFull(),
            ]),
            Section::make('Media sosial')->columns(2)
                ->description('Tempel tautan lengkap profil, diawali https://. Yang dikosongkan tidak ditampilkan di website.')
                ->schema([
                    $social('instagram', 'Instagram'),
                    $social('facebook', 'Facebook'),
                    $social('tiktok', 'TikTok'),
                    $social('threads', 'Threads'),
                    $social('youtube', 'YouTube'),
                    TextInput::make('data.hr_instagram')->label('Instagram lowongan kerja (nama akun, tanpa @)')->maxLength(60)
                        ->helperText('Contoh: lokerseafoodmuphus'),
                    Repeater::make('data.social_extra')->label('Media sosial lainnya')->columnSpanFull()
                        ->helperText('Untuk akun baru yang belum punya kolom sendiri, misalnya LinkedIn atau Pinterest. Muncul di footer dan halaman Kontak.')
                        ->schema([
                            TextInput::make('label')->label('Nama')->required()->maxLength(30),
                            TextInput::make('url')->label('Tautan')->required()->url()->maxLength(300)
                                ->regex('/^https:\/\//')->validationMessages(['regex' => 'Tautan harus diawali https://']),
                        ])
                        ->columns(2)->reorderable()->collapsible()->maxItems(10)
                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                        ->addActionLabel('Tambah akun'),
                ]),
            Section::make('Statistik pengunjung')->columns(1)
                ->description('Hubungkan ke Google Analytics 4 untuk melihat jumlah pengunjung, halaman yang dibuka, dan asal pengunjung. Dikosongkan = pelacakan mati dan banner cookie tidak muncul.')
                ->schema([
                    TextInput::make('data.analytics_id')->label('ID pengukuran Google Analytics')
                        ->placeholder('G-XXXXXXXXXX')->maxLength(20)
                        ->regex('/^G-[A-Z0-9]{4,15}$/')->validationMessages(['regex' => 'Format ID harus seperti G-ABC123XYZ9.'])
                        ->helperText('Cari di Google Analytics: Admin > Aliran data > pilih situs web > "ID pengukuran". Pelacakan baru aktif setelah pengunjung menekan Terima di banner cookie.'),
                ]),
            Section::make('Angka perusahaan')->columns(2)->schema([
                TextInput::make('data.founded')->label('Tahun berdiri')->maxLength(10),
                TextInput::make('data.target_outlets')->label('Target outlet')->maxLength(20),
                TextInput::make('data.target_year')->label('Tahun target')->maxLength(10),
                TextInput::make('data.min_land')->label('Luas lahan minimal')->maxLength(30),
            ]),
        ];
    }

    // ---------- Pembantu ----------

    private static function text(string $key, string $label, int $max = 80): TextInput
    {
        return TextInput::make("data.{$key}")->label($label)->maxLength($max);
    }

    private static function para(string $key, string $label, int $rows = 3, int $max = 500): Textarea
    {
        return Textarea::make("data.{$key}")->label($label)->rows($rows)->maxLength($max);
    }

    /** Daftar judul + keterangan (atau kolom lain) yang bisa ditambah, dihapus, dan diurutkan. */
    private static function items(string $key, string $label, int $min, int $max, array $extra = []): Repeater
    {
        return Repeater::make("data.{$key}")->label($label)
            ->schema(array_merge([
                TextInput::make('title')->label('Judul')->required()->maxLength(80),
                Textarea::make('text')->label('Keterangan')->rows(2)->maxLength(300),
            ], $extra))
            ->minItems($min)->maxItems($max)->reorderable()->collapsible()
            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
            ->addActionLabel('Tambah');
    }

    private static function image(string $key, string $label): FileUpload
    {
        return FileUpload::make("data.{$key}")->label($label)
            ->disk('public')->directory('site')->image()->maxSize(11264)
            ->saveUploadedFileUsing(ImageOptimizer::saver('site', 1600, 82))
            ->helperText('Kosongkan untuk memakai foto bawaan. Otomatis dikecilkan ke WebP.');
    }

    /**
     * Gambar dengan editor: bisa diputar, dibalik, dan dipotong. Di situs, gambar selalu ditampilkan utuh
     * di dalam bingkai (tanpa terpotong), jadi tidak perlu dipas manual.
     */
    private static function imageWithEditor(string $key, string $label, array $ratios): FileUpload
    {
        return self::image($key, $label)
            ->imageEditor()
            ->imageEditorEmptyFillColor('#FFFFFF')
            ->imageEditorAspectRatioOptions($ratios)
            ->helperText('Setelah memilih gambar, klik ikon pensil untuk memutar (kiri/kanan), membalik, atau memotong. Kosongkan untuk memakai gambar bawaan.');
    }

    private static function faq(string $heading): Section
    {
        return Section::make($heading)->collapsible()->collapsed()->columns(1)->description('Isi pertanyaan dan jawabannya ada di menu FAQ.')->schema([
            self::text('faq_eyebrow', 'Label kecil di atas judul', 60),
            self::title('faq_title'),
            self::para('faq_text', 'Paragraf', 2, 300),
            self::text('faq_button', 'Tulisan tautan WhatsApp', 40),
        ]);
    }

    // ---------- Kemitraan (khusus superadmin) ----------

    private static function partnership(): array
    {
        return [
            Section::make('Pengantar')->collapsible()->columns(1)->schema([
                self::text('badge', 'Label merah di atas judul', 60)->helperText('Isi tanda - (minus) untuk menyembunyikan label.'),
                self::text('intro_eyebrow', 'Label kecil di atas judul', 60),
                self::title('intro_title'),
                self::para('intro_text', 'Paragraf', 3, 400),
                self::text('stat_1_label', 'Keterangan angka 1 (jumlah cabang)', 40),
                self::text('stat_2_label', 'Keterangan angka 2 (luas lahan)', 40),
                self::text('stat_3_value', 'Angka 3', 20),
                self::text('stat_3_label', 'Keterangan angka 3', 40),
                self::text('wa_label', 'Tulisan tombol WhatsApp', 40),
                self::para('wa_message', 'Pesan awal WhatsApp', 2, 200),
                self::text('land_link_label', 'Tulisan tautan spesifikasi lahan', 60),
            ]),
            Section::make('Foto halaman')->collapsible()->collapsed()->columns(2)->schema([
                self::image('img_main', 'Foto utama'),
                self::imageWithEditor('img_plan', 'Denah warung (tampil utuh, tidak terpotong)', ['3:5', null, '1:1', '4:3', '5:3']),
                self::image('img_front', 'Tampak depan'),
                self::image('img_inside', 'Tampak dalam'),
            ]),
            Section::make('Spesifikasi lahan')->collapsible()->collapsed()->columns(2)->schema([
                self::text('land_eyebrow', 'Label kecil', 60),
                self::title('land_title'),
                self::text('land_area', 'Luas minimal', 40),
                self::text('land_width', 'Lebar depan', 40),
                self::text('land_depth', 'Panjang', 40),
                self::text('land_road', 'Lebar jalan', 40),
                self::text('land_status', 'Status lahan', 80),
                self::para('land_note', 'Catatan di bawah daftar', 2, 300)->columnSpanFull(),
            ]),
            Section::make('Lokasi strategis')->collapsible()->collapsed()->columns(1)->schema([
                self::text('place_eyebrow', 'Label kecil', 60),
                self::title('place_title'),
                self::items('places', 'Butir lokasi', 1, 6),
            ]),
            Section::make('Sistem kerja sama')->collapsible()->collapsed()->columns(1)->schema([
                self::text('deal_eyebrow', 'Label kecil', 60),
                self::title('deal_title'),
                self::para('deal_text', 'Paragraf', 2, 300),
                self::items('schemes', 'Skema bagi hasil', 1, 4, [
                    TextInput::make('label')->label('Label (mis. Skema 1)')->maxLength(30),
                    TextInput::make('figure')->label('Angka besar (mis. 50%)')->maxLength(20),
                ]),
                self::para('deal_note', 'Catatan di bawah skema', 2, 200),
            ]),
            Section::make('Alur kerja sama')->collapsible()->collapsed()->columns(1)->schema([
                self::text('flow_eyebrow', 'Label kecil', 60),
                self::title('flow_title'),
                self::items('steps', 'Langkah-langkah', 1, 15),
            ]),
            Section::make('Alasan bermitra')->collapsible()->collapsed()->columns(1)->schema([
                self::text('why_eyebrow', 'Label kecil', 60),
                self::title('why_title'),
                self::items('reasons', 'Alasan', 1, 8)->helperText('Tulis {cabang} untuk memakai jumlah cabang aktif secara otomatis.'),
            ]),
            self::faq('Pertanyaan umum'),
            Section::make('Ajakan di bagian bawah')->collapsible()->collapsed()->columns(1)->schema([
                self::text('cta_eyebrow', 'Label kecil', 60),
                self::title('cta_title'),
                self::para('cta_text', 'Paragraf', 3, 300),
                self::text('cta_wa_label', 'Tulisan tombol WhatsApp', 40),
                self::text('cta_form_label', 'Tulisan tombol formulir', 40),
            ]),
        ];
    }

    // ---------- Karir ----------

    private static function careers(): array
    {
        return [
            Section::make('Temukan lowongan')->collapsible()->columns(1)->description('Banner halaman diganti lewat menu "Banner halaman". Brosur lowongan lewat menu Lowongan.')->schema([
                self::text('list_eyebrow', 'Label kecil', 60),
                self::title('list_title'),
                self::para('list_text', 'Paragraf', 3, 300),
                self::para('empty_text', 'Tulisan bila lowongan kosong', 2, 300)->helperText('Tulis {kategori} dan {email} agar terisi otomatis.'),
            ]),
            Section::make('Karir outlet')->collapsible()->collapsed()->columns(1)->schema([
                self::text('outlet_eyebrow', 'Label kecil', 60),
                self::title('outlet_title'),
                self::para('outlet_text', 'Paragraf', 2, 300),
            ]),
            Section::make('Karir office')->collapsible()->collapsed()->columns(1)->schema([
                self::text('office_eyebrow', 'Label kecil', 60),
                self::title('office_title'),
                self::para('office_text', 'Paragraf', 2, 300),
            ]),
            Section::make('Cara melamar')->collapsible()->collapsed()->columns(1)->schema([
                self::text('steps_eyebrow', 'Label kecil', 60),
                self::title('steps_title'),
                self::items('steps', 'Langkah', 1, 6),
            ]),
            Section::make('Kenapa Muphus')->collapsible()->collapsed()->columns(1)->schema([
                self::text('why_eyebrow', 'Label kecil', 60),
                self::text('why_company', 'Nama perusahaan', 120),
                self::title('why_title'),
                self::para('why_text', 'Paragraf', 3, 300),
                self::items('benefits', 'Benefit', 1, 12),
                self::text('why_note', 'Catatan di bawah benefit', 120),
            ]),
            self::faq('Pertanyaan umum'),
        ];
    }

    // ---------- Kontak ----------

    private static function contact(): array
    {
        return [
            Section::make('Hubungi kami')->collapsible()->columns(1)->description('Alamat, nomor, email, dan media sosial diubah di Pengaturan Umum (khusus superadmin).')->schema([
                self::text('intro_eyebrow', 'Label kecil', 60),
                self::title('intro_title'),
                self::para('intro_text', 'Paragraf', 3, 300),
                self::text('hours', 'Jam layanan', 120)->helperText('Isi tanda - (minus) untuk menyembunyikan baris jam layanan.'),
            ]),
            Section::make('Formulir')->collapsible()->collapsed()->columns(1)->schema([
                self::text('form_title', 'Judul formulir', 60),
                self::text('chat_note', 'Tulisan ajakan chat', 120),
                self::text('chat_button', 'Tulisan tombol chat', 40),
                self::text('success_title', 'Judul setelah terkirim', 60),
                self::para('success_text', 'Tulisan setelah terkirim', 2, 300),
            ]),
            self::faq('Pertanyaan umum'),
        ];
    }

    /** Daftar yang isinya hanya satu kolom teks (paragraf, misi). */
    private static function lines(string $key, string $label, int $min, int $max, int $rows = 3): Repeater
    {
        return Repeater::make("data.{$key}")->label($label)
            ->schema([Textarea::make('text')->label('Teks')->required()->rows($rows)->maxLength(1500)])
            ->minItems($min)->maxItems($max)->reorderable()->collapsible()
            ->itemLabel(fn (array $state): ?string => isset($state['text']) ? \Illuminate\Support\Str::limit($state['text'], 50) : null)
            ->addActionLabel('Tambah');
    }

    private static function cta(string $heading, bool $withTexts = true): Section
    {
        return Section::make($heading)->collapsible()->collapsed()->columns(1)->schema([
            self::text('cta_eyebrow', 'Label kecil', 60),
            self::title('cta_title'),
            self::para('cta_text', 'Paragraf', 2, 300)->helperText('Tulis {cabang} untuk memakai jumlah cabang aktif secara otomatis.'),
            self::text('cta_1_label', 'Tulisan tombol 1', 40),
            self::text('cta_2_label', 'Tulisan tombol 2', 40),
        ]);
    }

    // ---------- Tentang (khusus superadmin) ----------

    private static function about(): array
    {
        $vars = 'Bisa memakai {cabang}, {tahun}, dan {target} agar terisi otomatis dari Pengaturan Umum.';

        return [
            Section::make('Sekilas profil')->collapsible()->columns(1)->description('Banner halaman diganti lewat menu "Banner halaman".')->schema([
                self::text('profile_eyebrow', 'Label kecil', 60),
                self::text('profile_company', 'Nama perusahaan', 120),
                self::title('profile_title'),
                self::lines('profile_paragraphs', 'Paragraf profil', 1, 10, 5)
                    ->helperText($vars . ' Baris baru di dalam satu paragraf akan ditampilkan sebagai baris baru.'),
                self::items('facts', 'Ringkasan (mis. Cara menikmati)', 0, 4),
                self::imageWithEditor('img_1', 'Foto profil (lebar, tampil di bawah teks; pakai foto landscape)', ['21:9', '16:9', '4:3', null]),
                self::text('stat_1_label', 'Keterangan angka 1 (jumlah cabang)', 40),
                self::text('stat_2_label', 'Keterangan angka 2 (tahun berdiri)', 40),
                self::text('stat_3_label', 'Keterangan angka 3 (target outlet)', 60)->helperText('Tulis {tahun} untuk tahun target.'),
            ]),
            Section::make('Perjalanan kami')->collapsible()->collapsed()->columns(1)->schema([
                self::text('journey_eyebrow', 'Label kecil', 60),
                self::title('journey_title'),
                self::items('journey', 'Tahapan (titik terakhir berwarna merah)', 2, 6, [
                    TextInput::make('label')->label('Penanda (tanggal atau tahun)')->maxLength(40),
                ])->helperText($vars),
            ]),
            Section::make('Standar kami')->collapsible()->collapsed()->columns(1)->schema([
                self::text('standards_eyebrow', 'Label kecil', 60),
                self::title('standards_title'),
                self::items('standards', 'Standar', 1, 8),
                self::para('standards_note', 'Kalimat penutup', 2, 200),
            ]),
            Section::make('Arah kami (visi, misi, nilai)')->collapsible()->collapsed()->columns(1)->schema([
                self::text('direction_eyebrow', 'Label kecil', 60),
                self::title('direction_title'),
                self::text('vision_label', 'Label visi', 30),
                self::para('vision_text', 'Isi visi', 3, 300)->helperText('Bungkus kata dengan bintang untuk ditebalkan hijau, misalnya *Market Leader*.'),
                self::text('mission_label', 'Label misi', 30),
                self::lines('missions', 'Misi', 1, 8, 2),
                self::text('value_eyebrow', 'Label core value', 40),
                self::text('value_title', 'Judul core value', 40),
                self::para('value_text', 'Paragraf core value', 2, 200),
                self::items('values', 'Nilai', 1, 10, [
                    TextInput::make('letter')->label('Huruf besar')->maxLength(2),
                ]),
            ]),
            self::cta('Ajakan di bagian bawah'),
        ];
    }

    // ---------- Menu ----------

    private static function menu(): array
    {
        return [
            Section::make('Pengantar')->collapsible()->columns(1)->description('Daftar menu dan fotonya diubah di menu "Menu". Banner lewat "Banner halaman".')->schema([
                self::text('intro_eyebrow', 'Label kecil', 60),
                self::text('intro_company', 'Nama perusahaan', 120),
                self::title('intro_title'),
                self::para('intro_text', 'Paragraf', 3, 300),
            ]),
            Section::make('Judul daftar menu')->collapsible()->collapsed()->columns(1)->schema([
                self::text('featured_title', 'Judul menu andalan', 60),
                self::text('more_cards_label', 'Tulisan tombol "lihat lebih banyak"', 60),
                self::text('list_title', 'Judul daftar lengkap', 60),
                self::text('list_note', 'Catatan harga', 120),
                self::text('spicy_note', 'Keterangan ikon cabai', 60),
            ]),
            Section::make('Cara menikmati')->collapsible()->collapsed()->columns(1)->schema([
                self::text('ways_eyebrow', 'Label kecil', 60),
                self::title('ways_title'),
                self::items('ways', 'Cara', 1, 6),
                self::para('ways_note', 'Kalimat penutup', 2, 200),
            ]),
            self::cta('Ajakan di bagian bawah'),
        ];
    }

    // ---------- Cabang ----------

    private static function branches(): array
    {
        return [
            Section::make('Temukan cabang')->collapsible()->columns(1)->description('Daftar cabang diubah di menu "Cabang". Banner lewat "Banner halaman".')->schema([
                self::text('intro_eyebrow', 'Label kecil', 60),
                self::title('title_1', 'Judul baris 1')->helperText('Tulis {cabang} untuk jumlah cabang otomatis. Bungkus dengan bintang untuk hijau miring.'),
                self::text('title_2', 'Judul baris 2', 120),
                self::para('intro_text', 'Paragraf', 2, 300),
                self::text('search_placeholder', 'Tulisan di kolom pencarian', 60),
                self::text('directions_label', 'Tulisan tombol petunjuk arah', 40),
                self::text('phone_label', 'Tulisan tombol telepon', 40),
            ]),
        ];
    }

    // ---------- Artikel ----------

    private static function articles(): array
    {
        return [
            Section::make('Info terbaru')->collapsible()->columns(1)->description('Artikelnya diubah di menu "Artikel". Banner lewat "Banner halaman".')->schema([
                self::text('intro_eyebrow', 'Label kecil', 60),
                self::title('intro_title'),
                self::text('search_placeholder', 'Tulisan di kolom pencarian', 60),
                self::text('read_label', 'Tulisan tautan baca artikel', 40),
                self::text('more_label', 'Tulisan tombol "lihat lebih banyak"', 60),
            ]),
            Section::make('Ajakan ikuti media sosial')->collapsible()->collapsed()->columns(1)->description('Tombol Instagram dan TikTok mengikuti Pengaturan Umum.')->schema([
                self::title('follow_title'),
                self::para('follow_text', 'Paragraf', 2, 300),
            ]),
        ];
    }
}