<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Branch;
use App\Models\Faq;
use App\Models\JobOpening;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Data ASLI yang diambil dari https://www.muphusseafood.com/ (5 November 2026).
 * Foto diunduh dari website yang sama ke storage/app/public/seed/.
 *
 * Aman dijalankan berulang (tidak membuat data ganda, tidak mengunduh ulang foto yang sudah ada).
 * Butuh koneksi internet.
 *
 * Jalankan: php artisan db:seed --class=RealContentSeeder
 */
class RealContentSeeder extends Seeder
{
    private const BASE = 'https://www.muphusseafood.com/wp-content/uploads/';

    public function run(): void
    {
        $this->menu();
        $this->branches();
        $this->jobs();
        $this->articles();
        $this->faqs();

        $this->command?->info('Data asli selesai dibuat.');
    }

    // ------------------------------------------------------------------ MENU
    private function menu(): void
    {
        // [nama, deskripsi, berkas foto, tampil di Beranda]
        $items = [
            ['Lobster', 'Daging lobster tebal dan juicy dengan saus asam manis yang gurih.', '2025/11/FEED-IG-LOBSTER.jpg', true],
            ['Kepiting Mix Lengkap', 'Kepiting dengan kerang, cumi, dan udang dalam satu wadah. Tingkat kepedasan bisa dipilih.', '2025/11/FEED-IG-KEPITING.jpg', true],
            ['Kepiting Saus Padang', 'Kepiting dalam saus Padang yang kental, pedas, sedikit manis, dan harum rempah.', '2025/11/FEED-IG-KEPITING-SAUS.jpg', true],
            ['Kerapu Bakar', 'Ikan bakar dengan bumbu yang meresap hingga ke dalam, dipanggang beraroma khas.', '2025/11/FEED-IG-KERAPU.jpg', true],
            ['Gurame Bakar', 'Gurame bakar bumbu oles khas dengan aroma asap, disajikan dengan sambal spesial.', '2025/11/FEED-IG-GURAME.jpg', false],
            ['Bawal Bakar', 'Daging bawal lembut dan juicy dengan bumbu bakar yang meresap.', '2025/11/FEED-IG-BAWAL.jpg', false],
            ['Gurame Mix Udang Cumi', 'Gurame, udang, dan cumi dalam satu menu. Bumbu bisa dipilih sesuai selera.', '2025/11/FEED-IG-GURAME-MIX.jpg', false],
            ['Kerang Dara', 'Kerang dara lembut dengan saus asam manis yang seimbang.', '2025/11/FEED-IG-KERANG-DARA.jpg', false],
            ['Kerang Hijau', 'Kerang hijau dengan saus lada hitam yang hangat dan beraroma kuat.', '2025/11/FEED-IG-KERANG-HIJAU.jpg', false],
        ];

        foreach ($items as $i => [$name, $description, $file, $home]) {
            MenuItem::updateOrCreate(
                ['name' => $name],
                [
                    'category' => 'seafood',
                    'description' => $description,
                    'price' => null, // harga belum ada di website
                    'image_path' => $this->download(self::BASE . $file, 'seed/menu/' . basename($file)),
                    'badge' => null,
                    'show_on_home' => $home,
                    'is_active' => true,
                    'sort_order' => $i,
                ]
            );
        }
    }

    // ---------------------------------------------------------------- CABANG
    private function branches(): void
    {
        // [nama, kota, alamat/area, latitude, longitude]
        // Koordinat dari peta tersemat di website. Kota diturunkan dari nama cabang.
        $branches = [
            ['Seafood Muphus Babat', 'Lamongan', 'Babat, Lamongan', -7.1151792, 112.1648395],
            ['Seafood Muphus Lamongan', 'Lamongan', 'Sanur, Lamongan', -7.1495917, 112.4107837],
            ['Seafood Muphus Gresik', 'Gresik', 'Duduksampeyan, Gresik', -7.1650582, 112.5481567],
            ['Seafood Muphus Surabaya Benowo', 'Surabaya', 'Benowo, Surabaya', -7.2430094, 112.6310949],
            ['Seafood Muphus Unesa', 'Surabaya', 'Unesa, Surabaya', -7.2982948, 112.6706293],
            ['Seafood Muphus Mojosari', 'Mojokerto', 'Mojosari, Mojokerto', -7.5098793, 112.5593831],
            ['Seafood Muphus Malang Akordion', 'Malang', 'Akordion, Malang', -7.9338582, 112.6139666],
            ['Seafood Muphus Malang Singosari', 'Malang', 'Singosari, Malang', -7.9007603, 112.6630478],
            ['Seafood Muphus Probolinggo', 'Probolinggo', 'Probolinggo', -7.7453364, 113.219111],
            ['Seafood Muphus Kraksaan', 'Probolinggo', 'Kraksaan, Probolinggo', -7.7557108, 113.4374816],
            ['Seafood Muphus Semarang', 'Semarang', 'Ungaran, Semarang', -7.1332199, 110.4093291],
            ['Seafood Muphus Yogyakarta', 'Yogyakarta', 'Yogyakarta', -7.8162403, 110.3864245],
            ['Seafood Muphus Solo Kartasura', 'Solo', 'Kartasura, Solo', -7.5636759, 110.7630872],
            ['Seafood Muphus Solo Laweyan', 'Solo', 'Laweyan, Solo', -7.5695873, 110.8051407],
            ['Seafood Muphus Solo Baru', 'Solo', 'Solo Baru', -7.6108267, 110.8061252],
            ['Seafood Muphus Kudus Peganjaran', 'Kudus', 'Peganjaran, Kudus', -6.7793415, 110.8356229],
            ['Seafood Muphus Jember', 'Jember', 'Jember', -8.1891252, 113.6853909],
            ['Seafood Muphus Solo Sumber', 'Solo', 'Sumber, Solo', -7.5551071, 110.7634504],
            ['Seafood Muphus Klaten', 'Klaten', 'Klaten', -7.69417, 110.5981094],
            ['Seafood Muphus Cirebon Sunyaragi', 'Cirebon', 'Sunyaragi, Cirebon', -6.7327908, 108.5379026],
            ['Seafood Muphus Jombang', 'Jombang', 'Jombang', -7.5287728, 112.2318948],
            ['Seafood Muphus Sragen', 'Sragen', 'Sragen', -7.4299016, 111.0060862],
            ['Seafood Muphus Boyolali', 'Boyolali', 'Boyolali', -7.5383415, 110.608465],
            ['Seafood Muphus Malang Kedung Kandang', 'Malang', 'Kedung Kandang, Malang', -7.9615471, 112.612228],
        ];

        foreach ($branches as $i => [$name, $city, $address, $lat, $lng]) {
            Branch::updateOrCreate(
                ['name' => $name],
                [
                    'city' => $city,
                    'address' => $address,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'maps_url' => null,
                    'whatsapp' => null,   // belum ada nomor per cabang
                    'opens_at' => null,   // jam buka belum ada datanya
                    'closes_at' => null,
                    'hours_note' => null,
                    'photo_path' => null,
                    'is_active' => true,
                    'sort_order' => $i,
                ]
            );
        }
    }

    // -------------------------------------------------------------- LOWONGAN
    private function jobs(): void
    {
        // Urutan brosur mengikuti halaman Karir di website. Periksa pasangannya di admin bila perlu.
        $jobs = [
            [
                'type' => 'outlet', 'position' => 'Crew Outlet Salatiga', 'city' => 'Salatiga', 'division' => null,
                'hook' => 'Jadi bagian dari tim outlet baru Seafood Muphus di Salatiga.',
                'summary' => '8 posisi: kepala warung, admin atau kasir, koki masak, koki bakar, koki goreng, bartender, helper, dan waiter. Siap training di Ungaran atau Solo.',
                'file' => '2026/04/WhatsApp-Image-2026-04-14-at-10.31.18.jpeg',
                'form_url' => 'https://bit.ly/lokermuphus',
            ],
            [
                'type' => 'office', 'position' => 'Staff Finance', 'city' => null, 'division' => 'Finance',
                'hook' => 'Jaga keuangan setiap unit tetap rapi dan akurat.',
                'summary' => 'Menyusun laporan keuangan unit, melakukan rekonsiliasi keuangan, dan audit keuangan ke unit. Penempatan di kantor pusat Tuban.',
                'file' => '2026/04/WhatsApp-Image-2026-04-11-at-14.24.16.jpeg',
                'form_url' => 'https://bit.ly/financemuphus',
            ],
            [
                'type' => 'outlet', 'position' => 'Tim Outlet Sragen', 'city' => 'Sragen', 'division' => null,
                'hook' => 'Bangun karir bersama outlet Seafood Muphus di Sragen.',
                'summary' => '5 posisi: pramusaji dan bartender, juru masak, kasir, admin, dan kepala warung. Penempatan di Sragen, Jawa Tengah.',
                'file' => '2025/11/Muphus-Seafood-Karir-1.jpeg',
                'form_url' => 'https://bit.ly/carermuphusseafood',
            ],
        ];

        foreach ($jobs as $i => $job) {
            $file = $job['file'];
            unset($job['file']);

            JobOpening::updateOrCreate(
                ['position' => $job['position']],
                $job + [
                    'brochure_path' => $this->download(self::BASE . $file, 'seed/karir/' . basename($file)),
                    'deadline' => null,
                    'is_active' => true,
                    'sort_order' => $i,
                ]
            );
        }
    }

    // --------------------------------------------------------------- ARTIKEL
    private function articles(): void
    {
        $articles = [
            [
                'slug' => 'seafood-muphus-deretan-menu-favorit-seafood',
                'title' => 'Seafood Muphus – Deretan Menu Favorit Seafood',
                'category' => 'menu',
                'excerpt' => 'Deretan menu favorit yang paling diburu pelanggan ketika berkunjung ke Seafood Muphus, dari Lobster Asam Manis hingga Kerang Hijau Lada Hitam.',
                'cover' => '2025/11/Muphus-Seafood-%E2%80%93-Deretan-Menu-Favorit-Seafood1.jpg',
                'featured' => true,
                'body' => $this->bodyMenuFavorit(),
            ],
            [
                'slug' => 'seafood-muphus-dari-kaki-lima-ke-60-cabang',
                'title' => 'Seafood Muphus: Dari Kaki Lima ke 60 Cabang',
                'category' => 'cerita',
                'excerpt' => 'Perjalanan Seafood Muphus dari outlet kaki lima pertama di Babat, Lamongan, menuju target 60 cabang di seluruh Indonesia.',
                'cover' => '2025/11/Muphus-Seafood-Dari-Kaki-Lima-ke-60-Cabang1.jpg',
                'featured' => false,
                'body' => $this->bodyKakiLimaKe60(),
            ],
            [
                'slug' => 'seafood-muphus-kuliner-kaki-lima-rasa-restoran',
                'title' => 'Seafood Muphus: Kuliner Kaki Lima Rasa Restoran',
                'category' => 'cerita',
                'excerpt' => 'Bagaimana Seafood Muphus membawa pengalaman kuliner kaki lima dengan cita rasa restoran dan harga yang tetap bersahabat.',
                'cover' => '2025/11/Muphus-Seafood-Kuliner-Kaki-Lima-Rasa-Restoran-21.jpg',
                'featured' => false,
                'body' => $this->bodyRasaRestoran(),
            ],
        ];

        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['slug' => $article['slug']],
                [
                    'title' => $article['title'],
                    'category' => $article['category'],
                    'excerpt' => $article['excerpt'],
                    // Penulisan nama merek diseragamkan menjadi "Seafood Muphus".
                    'body' => str_replace('Muphus Seafood', 'Seafood Muphus', $article['body']),
                    'cover_path' => $this->download(self::BASE . $article['cover'], 'seed/artikel/' . $article['slug'] . '.jpg'),
                    'status' => 'published',
                    'published_at' => '2025-11-21 09:00:00',
                    'is_featured' => $article['featured'],
                ]
            );
        }
    }

    // ------------------------------------------------------------------- FAQ
    private function faqs(): void
    {
        $faqs = [
            ['Bagaimana caranya melamar pekerjaan?', 'Anda dapat melamar pekerjaan dengan mengirimkan CV, portofolio (jika diperlukan), dan surat lamaran melalui email atau form pendaftaran yang telah disediakan. Pastikan semua berkas lengkap dan sesuai persyaratan posisi yang Anda inginkan.'],
            ['Bagaimana caranya mengetahui apakah saya lolos atau tidak?', 'Anda akan mendapatkan informasi terkait proses seleksi melalui email. Jadi, pastikan email yang digunakan untuk mendaftar adalah email aktif ya.'],
            ['Berapa gaji untuk karyawan?', 'Informasi terkait gaji akan disampaikan oleh tim rekrutmen ketika Anda dinyatakan lolos seluruh tahapan seleksi.'],
        ];

        foreach ($faqs as $i => [$question, $answer]) {
            Faq::updateOrCreate(
                ['page' => 'karir', 'question' => $question],
                ['answer' => $answer, 'sort_order' => $i, 'is_active' => true]
            );
        }
    }

    // ----------------------------------------------------------------- FOTO
    /** Unduh foto dari website ke disk "public". Mengembalikan jalur relatif, atau null bila gagal. */
    private function download(string $url, string $path): ?string
    {
        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

        try {
            $response = Http::timeout(60)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; MuphusSeeder)'])
                ->get($url);

            if ($response->successful() && strlen($response->body()) > 1000) {
                Storage::disk('public')->put($path, $response->body());

                return $path;
            }
        } catch (\Throwable $e) {
            // dilewati di bawah
        }

        $this->command?->warn('Foto gagal diunduh: ' . $url);

        return null;
    }

    // ------------------------------------------------------ ISI ARTIKEL
    private function bodyMenuFavorit(): string
    {
        return <<<'MD'
Muphus Seafood semakin dikenal sebagai salah satu destinasi kuliner laut terbaik karena mampu menyajikan hidangan segar, kaya rasa, dan selalu mengundang selera. Setiap hari, para pengunjung datang dari berbagai daerah untuk menikmati aneka menu andalan yang tidak hanya lezat, tetapi juga diolah dengan teknik memasak yang menjaga kualitas rasa dan kesegarannya. Berikut adalah deretan menu favorit yang paling diburu pelanggan ketika berkunjung ke Muphus Seafood.

## 1. Lobster Asam Manis

Lobster merupakan salah satu hidangan premium yang selalu menjadi incaran para pecinta seafood. Di Muphus Seafood, Lobster Asam Manis menjadi menu yang paling menonjol, berkat perpaduan bumbu khas yang meresap hingga ke setiap serat daging lobster.

Cita rasa manis, asam, dan gurih berpadu sempurna dalam sausnya. Daging lobster yang tebal dan juicy semakin membuat hidangan ini terasa mewah. Proses pengolahannya pun dilakukan dengan sangat hati-hati, memastikan daging tetap lembut tanpa kehilangan tekstur aslinya. Tidak heran jika Lobster Asam Manis menjadi salah satu menu yang wajib dicoba saat berkunjung.

## 2. Kepiting Mix Lengkap

Menu **Kepiting Mix Lengkap** menjadi favorit keluarga karena menawarkan kombinasi hidangan kepiting dengan tambahan bermacam seafood lain seperti kerang, cumi, dan udang. Semua disajikan dalam satu wadah besar sehingga memberikan pengalaman makan yang lebih seru dan memuaskan.

Yang membuat menu ini spesial adalah bumbu khas racikan Muphus Seafood yang meresap ke seluruh isian. Pengunjung bisa memilih tingkat kepedasan sesuai selera, mulai dari level ringan hingga super pedas. Dengan porsi besar dan rasa yang kaya, Kepiting Mix Lengkap cocok menjadi pilihan saat makan ramai-ramai bersama teman atau keluarga.

## 3. Gurami Bakar

Ikan gurami adalah salah satu ikan paling digemari karena teksturnya yang lembut serta rasanya yang gurih. Muphus Seafood menyajikan **Gurami Bakar** dengan sentuhan bumbu oles khas yang memberikan aroma asap menggugah selera.

Proses pembakaran dengan arang berkualitas membuat ikan memiliki tekstur luar yang sedikit crispy namun tetap lembut di bagian dalam. Gurami Bakar sangat cocok dinikmati dengan sambal spesial Muphus Seafood yang memiliki rasa pedas segar, asam, dan sedikit manis. Menu ini selalu masuk dalam daftar pesanan terbanyak setiap hari.

## 4. Bawal Bakar

Selain gurami, ikan bawal juga menjadi primadona di Muphus Seafood. **Bawal Bakar** di sini selalu mendapatkan pujian karena rasa dagingnya yang lembut dan juicy. Bumbu bakar yang meresap ke seluruh bagian ikan membuat setiap suapan terasa mantap.

Ikan bawal terkenal memiliki daging yang tidak mudah hancur, sehingga cocok dijadikan menu bakar. Aroma harum dari proses pemanggangan berpadu dengan sambal bawang atau sambal dabu-dabu yang disajikan sebagai pendamping, menjadikan Bawal Bakar pilihan tepat bagi pencinta ikan bakar berbumbu lengkap.

## 5. Kepiting Saus Padang

Menu klasik yang tidak pernah lekang oleh waktu di dunia seafood adalah **Kepiting Saus Padang**. Hidangan ini menjadi salah satu menu legendaris di Muphus Seafood yang tidak pernah sepi peminat.

Kepiting dimasak dengan saus Padang khas yang memiliki cita rasa pedas, sedikit manis, dan harum rempah. Tekstur saus yang kental membuatnya mudah menempel pada daging kepiting. Ini memberikan sensasi makan yang luar biasa saat daging kepiting dikupas dan dicampur dengan sausnya.

Perpaduan cabai, bawang, jahe, dan rempah-rempah lainnya menjadikan Kepiting Saus Padang sebagai hidangan yang selalu bikin ketagihan.

## 6. Gurami Udang Cumi

Bagi pengunjung yang ingin merasakan tiga jenis seafood dalam satu menu, **Gurami Udang Cumi** adalah jawabannya. Hidangan ini diracik dengan bumbu spesial yang menyatukan rasa gurami yang gurih, udang yang manis dan juicy, serta cumi yang kenyal.

Menu ini hadir dengan porsi besar sehingga cocok disantap bersama keluarga. Tidak hanya kaya protein, perpaduan tiga jenis seafood ini menciptakan harmoni rasa yang jarang ditemukan di restoran lain. Bumbu yang digunakan dapat disesuaikan, mulai dari asam manis, pedas manis, hingga lada hitam.

## 7. Kerang Dara Asam Manis

Kerang dara memiliki tekstur khas yang lembut dan sedikit kenyal. Di Muphus Seafood, menu **Kerang Dara Asam Manis** menjadi salah satu pilihan favorit para pengunjung.

Saus asam manis yang digunakan memiliki komposisi seimbang, tidak terlalu asam dan tidak terlalu manis. Proses memasak yang tepat membuat kerang tetap juicy dan tidak berbau amis. Menu ini enak disantap sebagai hidangan utama maupun tambahan pendamping untuk menu seafood lainnya.

## 8. Kerang Hijau Lada Hitam

Kerang hijau terkenal sebagai salah satu seafood yang kaya nutrisi. Ketika diolah dengan saus lada hitam ala Muphus Seafood, hasilnya adalah hidangan yang memberikan sensasi pedas hangat yang khas.

**Kerang Hijau Lada Hitam** ini memiliki aroma yang kuat, saus yang kaya rasa, dan tekstur kerang yang lembut. Perpaduan bawang bombay, lada hitam, dan bumbu rempah lainnya membuat menu ini menjadi favorit bagi pengunjung yang menyukai hidangan beraroma kuat dan sedikit pedas.

Menu ini juga cocok dijadikan teman makan nasi hangat karena saus lada hitamnya yang melimpah dan nikmat.

## Muphus Seafood, Surga Kuliner Laut untuk Semua Selera

Deretan menu favorit di atas membuktikan bahwa Muphus Seafood tidak hanya mengandalkan kesegaran bahan, tetapi juga keahlian dalam mengolah setiap hidangan menjadi sesuatu yang istimewa. Mulai dari lobster premium hingga kerang hijau berbumbu lada hitam, semua diolah dengan penuh perhatian dan cita rasa yang konsisten.

Muphus Seafood adalah tempat terbaik untuk menikmati hidangan laut bersama keluarga, pasangan, maupun teman. Dengan ragam menu yang lengkap dan rasa yang tak pernah mengecewakan, tidak mengherankan jika restoran ini terus menjadi pilihan utama bagi para pencinta seafood.

Apapun selera Anda—pedas, manis, gurih, atau perpaduan semuanya—Muphus Seafood selalu memiliki menu favorit yang siap memanjakan lidah Anda.
MD;
    }

    private function bodyKakiLimaKe60(): string
    {
        return <<<'MD'
Industri kuliner di Indonesia terus berkembang dengan sangat pesat. Setiap tahun, bermunculan usaha makanan baru dengan inovasi rasa, konsep, dan harga yang beragam. Namun hanya sedikit yang mampu bertahan, apalagi berkembang secara konsisten hingga memiliki banyak cabang. Salah satu brand yang berhasil menembus tantangan tersebut adalah Muphus Seafood, sebuah usaha kuliner lokal yang bermula dari konsep kaki lima namun kini menargetkan ekspansi hingga 60 cabang di seluruh Indonesia.

Perjalanan Muphus Seafood adalah perjalanan inspiratif tentang konsistensi kualitas, inovasi dalam penyajian kuliner, serta manajemen modern yang kuat. Dengan fokus pada cita rasa, harga terjangkau, dan pengalaman makan yang memuaskan, brand ini berhasil mencuri hati masyarakat dari berbagai kalangan. Dari mahasiswa, pekerja, hingga keluarga, semua menemukan alasan untuk kembali menikmati hidangan di Muphus Seafood.

## Awal Mula yang Sederhana di Babat, Lamongan

Muphus Seafood berdiri pada 26 Januari 2022 di Babat, Lamongan, Jawa Timur. Wilayah ini dikenal sebagai salah satu pusat kuliner kaki lima yang berkembang pesat. Banyak usaha kuliner yang lahir dari daerah ini, dan Muphus Seafood menjadi salah satu di antaranya, namun dengan konsep yang lebih modern dan terstruktur.

Outlet pertama ini menjadi pondasi bagi perjalanan panjang Muphus Seafood. Dengan menu sederhana seperti pecel lele, cumi krispi, udang goreng, seafood saus padang, serta beberapa hidangan favorit masyarakat, brand ini mulai mendapatkan perhatian pelanggan berkat cita rasa yang khas, bumbu yang meresap, dan harga yang tetap terjangkau.

Dari sinilah reputasi Muphus Seafood terbentuk—kuliner kaki lima yang menyajikan rasa restoran.

## Mengusung Konsep Kuliner Kaki Lima Berstandar Restoran

Salah satu alasan utama Muphus Seafood cepat berkembang adalah konsep yang sangat relevan dengan kebutuhan masyarakat saat ini. Orang Indonesia menyukai makanan enak, cepat disajikan, dan harganya bersahabat. Namun dalam beberapa tahun terakhir, tuntutan kualitas semakin meningkat.

Muphus Seafood melihat peluang ini dengan menghadirkan:

### 1. Menu Berkualitas, Rasa Konsisten

Setiap cabang wajib mengikuti standar resep yang sama. Bumbu khas yang digunakan diproses dengan metode yang memastikan keseragaman rasa, sehingga pelanggan di Lamongan, Semarang, atau Surabaya bisa merasakan sensasi yang identik.

### 2. Porsi Pas dan Harga Terjangkau

Porsi makanan diatur agar tetap mengenyangkan namun tidak membebani harga. Inilah yang menjadi nilai jual utama: rasa restoran, harga kaki lima.

### 3. Penyajian Modern

Meski berkonsep kaki lima, setiap cabang didesain agar lebih rapi, bersih, dan nyaman. Pengunjung bisa menikmati suasana makan sederhana namun tetap profesional.

### 4. Layanan Cepat dan Ramah

Crew Muphus Seafood dilatih untuk memberikan pelayanan cepat, karena karakter pengunjung street food cenderung menginginkan hidangan yang datang dalam waktu singkat.

## Perkembangan Pesat Menuju 24 Cabang

Setelah mendapatkan respon positif di cabang pertama, Muphus Seafood mulai membuka cabang di berbagai kota dan kabupaten di Jawa Timur, diikuti ekspansi ke Jawa Tengah.

Pada pertengahan 2025, Muphus Seafood resmi memiliki 24 cabang aktif. Pertumbuhan ini bukan hanya karena banyaknya permintaan konsumen, tetapi juga karena manajemen terpusat dari PT. Indonesia Menembus Gravitasi (PT. IMEG) yang menerapkan sistem operasional terukur dan mudah direplikasi.

Dukungan dari pelanggan yang terus bertambah membuat brand ini semakin percaya diri untuk memperluas jangkauan dan memenuhi permintaan pasar yang semakin besar.

## Target Ambisius: 60 Cabang di Seluruh Indonesia

Langkah berikutnya yang sedang dikejar oleh Muphus Seafood adalah ekspansi besar menuju 60 cabang nasional. Rencana ini merupakan komitmen besar perusahaan untuk menghadirkan hidangan lezat ke lebih banyak kota dan memberikan pengalaman kuliner berkualitas kepada masyarakat dari berbagai daerah.

Ekspansi ini dilakukan dengan mempertimbangkan:

### 1. Potensi Kota dan Perilaku Konsumen

Tidak semua kota cocok untuk usaha kuliner tertentu. Muphus memilih lokasi berdasarkan data kepadatan penduduk, kebiasaan makan, dan tren kuliner.

### 2. Standarisasi Kualitas

Semakin banyak cabang, semakin besar tantangan menjaga konsistensi rasa. Muphus menyikapinya dengan SOP ketat, distribusi bumbu terpusat, serta pelatihan kru yang rutin.

### 3. Manajemen Modern

Setiap cabang didukung sistem manajemen yang mudah dipantau, mulai dari stok, keuangan, hingga kualitas operasional harian.

Dengan pendekatan ini, target 60 cabang bukan hanya mimpi, tetapi langkah realistis yang sedang diarahkan dengan rencana matang.

## Berperan Besar dalam Membuka Lapangan Kerja

Seiring berkembangnya cabang, rekrutmen Muphus Seafood juga semakin terbuka. Banyak anak muda bergabung sebagai crew, kasir, koki, hingga supervisor. Brand ini tidak hanya menjalankan bisnis kuliner, tetapi juga berkontribusi pada pengurangan pengangguran lokal dan memberikan kesempatan kerja yang stabil.

PT. IMEG sebagai perusahaan induk juga mengelola sistem rekrutmen yang modern, serupa dengan model yang diterapkan oleh brand besar seperti Mie Gacoan.

## Kekuatan Muphus Seafood: Kenapa Bisa Berkembang Cepat?

Ada beberapa faktor utama yang menjadi kunci keberhasilan:

### 1. Brand Positioning yang Jelas

Muphus bukan sekadar pecel lele atau seafood kaki lima—ini adalah pengalaman makan yang lebih profesional.

### 2. Harga Bersaing

Dengan ekonomi yang fluktuatif, makanan terjangkau dengan rasa premium selalu diminati.

### 3. Menu Mudah Diterima Semua Orang

Variasi menu Muphus bersifat universal dan cocok untuk lidah masyarakat Indonesia.

### 4. Sistem Manajemen Kuat

Didukung PT. IMEG, ekspansi dan operasional berjalan rapi dan konsisten.

### 5. Promosi Media Sosial Efektif

Melalui Instagram dan TikTok, Muphus Seafood semakin dikenal oleh masyarakat luas.

## Penutup

Muphus Seafood telah membuktikan bahwa bisnis kuliner tidak harus dimulai dengan modal besar untuk menjadi brand besar. Dari sebuah outlet kaki lima sederhana di Babat, Lamongan, usaha ini berkembang pesat hingga puluhan cabang dan menatap target 60 cabang nasional.

Dengan kualitas rasa yang stabil, harga terjangkau, pelayanan ramah, serta strategi manajemen modern, Muphus Seafood pantas menjadi salah satu ikon kuliner kaki lima yang naik kelas di Indonesia.

Perjalanan ini baru dimulai, dan bukan tidak mungkin dalam beberapa tahun ke depan Muphus Seafood akan menjadi salah satu jaringan kuliner terbesar di tanah air.
MD;
    }

    private function bodyRasaRestoran(): string
    {
        return <<<'MD'
Indonesia dikenal sebagai salah satu negara dengan kekayaan kuliner terbesar di dunia. Dari Sabang sampai Merauke, ragam hidangan tradisional hingga masakan modern dapat ditemukan di setiap sudut kota. Namun, ada satu segmen kuliner yang tidak pernah kehilangan penggemarnya: kuliner kaki lima dengan cita rasa restoran. Inilah yang berhasil diwujudkan oleh Muphus Seafood, sebuah brand kuliner lokal yang membawa pengalaman makan ala street food menjadi lebih berkelas tanpa menghilangkan ciri khas harga yang ramah di kantong.

Sejak pertama kali berdiri pada 26 Januari 2022, Muphus Seafood hadir membawa misi sederhana namun kuat—menyajikan hidangan pecel lele dan aneka seafood segar dengan rasa lezat, porsi pas, dan harga yang bersahabat untuk semua kalangan. Dari sebuah outlet pertama di Babat, Lamongan, Jawa Timur, kini Muphus Seafood berkembang pesat hingga memiliki 24 cabang yang tersebar di Jawa Timur dan Jawa Tengah, dan menargetkan 60 cabang di Indonesia dalam waktu dekat.

Perjalanan ini bukanlah kebetulan. Ada dedikasi, kualitas, dan konsistensi yang membuat Muphus Seafood menjadi salah satu brand kuliner kaki lima yang naik kelas dan semakin dikenal luas oleh masyarakat.

## Konsep Kaki Lima, Kualitas Rasa Restoran

Muphus Seafood mengusung konsep sederhana namun berdampak besar: menggabungkan kenyamanan kuliner kaki lima dengan kualitas rasa seperti restoran. Hal ini dapat terlihat dari cara penyajian, pemilihan bahan baku, hingga racikan bumbu khas yang menjadi identitas rasa Muphus.

Beberapa faktor yang membuatnya digemari:

### 1. Bahan Baku Segar

Salah satu kunci cita rasa Muphus Seafood adalah kesegaran bahan-bahan yang digunakan. Ikan, udang, cumi, dan aneka seafood lainnya dipastikan dalam kondisi baik sebelum diolah. Hal ini memberikan tekstur lebih lembut, manis alami, dan aroma yang menggugah selera.

### 2. Bumbu Racikan Khas

Bumbu khas Muphus Seafood disiapkan dengan metode yang konsisten, memastikan setiap cabang memiliki kualitas rasa yang sama. Bumbu ini dibuat untuk memperkuat cita rasa tanpa membuat hidangan terlalu pedas, terlalu asin, atau terlalu gurih—seimbang dan dapat dinikmati oleh semua kalangan.

### 3. Teknik Masak Profesional

Meski mengusung konsep kaki lima, teknik memasaknya tidak asal-asalan. Muphus Seafood menerapkan standar operasional restoran, sehingga hasil masakan selalu matang merata, beraroma sedap, dan punya rasa yang konsisten.

## Menu Andalan yang Membuat Pelanggan Kembali Lagi

Muphus Seafood dikenal dengan menu pecel lele dan seafood yang variatif. Berikut beberapa menu favorit yang selalu menjadi incaran pelanggan:

### 1. Pecel Lele Muphus

Lele goreng garing di luar namun lembut di dalam, disajikan dengan sambal khas penuh rasa, lalapan segar, dan nasi hangat. Ini adalah menu yang tidak pernah absen dari pesanan.

### 2. Udang Saus Mentega

Udang segar yang ditumis dengan saus mentega membuat aroma harum dan rasa gurih manis yang menggugah selera. Salah satu menu paling populer di seluruh cabang.

### 3. Cumi Krispi

Cumi dipotong ringkas, dibalur tepung tipis, lalu digoreng garing. Renyah dan enak disantap kapan saja, cocok untuk semua usia.

### 4. Kerang Saus Padang

Kerang segar dengan kuah saus padang khas Muphus yang pedas-gurih, memberikan sensasi makan yang menggigit namun nagih.

### 5. Ikan Bakar Khas Muphus

Ikan bakar dengan bumbu meresap hingga ke dalam, dipanggang hingga menghasilkan aroma khas yang menjadikan pelanggan sulit melupakan rasanya.

Ragam menu ini membuktikan bahwa meski berawal dari kaki lima, Muphus Seafood mampu memberikan sajian yang layak bersaing dengan restoran berkelas.

## Harga Terjangkau untuk Semua Kalangan

Salah satu tujuan Muphus Seafood adalah memberikan kesempatan bagi siapa saja untuk menikmati seafood segar tanpa perlu mengeluarkan biaya besar. Harga menu disusun agar dapat dijangkau oleh berbagai kalangan—mulai dari pekerja, mahasiswa, keluarga, hingga pecinta kuliner yang ingin makan enak dengan harga bersahabat.

Kombinasi harga terjangkau, porsi pas, dan cita rasa tinggi menjadi daya tarik utama yang membuat Muphus Seafood selalu ramai pelanggan.

## Perjalanan Ekspansi yang Pesat

Di balik kesuksesan Muphus Seafood, terdapat perusahaan induk yaitu PT. Indonesia Menembus Gravitasi (PT. IMEG). Dengan semangat maju dan manajemen yang solid, brand ini mampu memperluas jangkauan bisnisnya dengan cepat.

Dari cabang pertama di Babat, Lamongan, Muphus Seafood kini telah merambah ke berbagai kota lainnya. Keberhasilan ini didukung oleh strategi operasional yang jelas, manajemen yang terstruktur, dan dukungan pelanggan yang terus bertambah.

Pada tahun 2025, Muphus Seafood resmi mencatat 24 cabang, dan target ekspansi menjadi 60 cabang di seluruh Indonesia bukan hal yang mustahil. Dengan sistem kerja yang terus diperbaiki, kualitas rasa yang konsisten, dan brand awareness yang meningkat, langkah ini semakin mudah dicapai.

## Kesempatan Karier di Muphus Seafood

Selain menyajikan hidangan lezat, Muphus Seafood juga membuka banyak peluang bagi generasi muda Indonesia. Melalui rekrutmen Muphus yang dilakukan secara berkala, brand ini memberikan kesempatan pekerjaan di berbagai posisi mulai dari cook, kasir, pramusaji, hingga crew outlet.

Persaingan dunia kuliner yang semakin ketat justru membuka jalan bagi Muphus Seafood untuk menghadirkan sistem kerja yang profesional, nyaman, dan penuh kesempatan berkembang. Hal ini membuat banyak pencari kerja antusias untuk bergabung.

## Komitmen Muphus Seafood

Muphus Seafood tidak hanya fokus pada ekspansi jumlah cabang, tetapi juga pada peningkatan kualitas. Setiap tahun, evaluasi rasa, layanan, dan sistem operasional dilakukan untuk memastikan pelanggan selalu mendapatkan pengalaman terbaik.

Komitmen tersebut meliputi:

- Menjaga kualitas bahan baku
- Meningkatkan standar layanan
- Mengembangkan varian menu baru
- Memperluas jangkauan ke kota-kota baru
- Membuka lebih banyak lapangan pekerjaan

Dengan semua langkah strategis ini, Muphus Seafood terus berupaya menjadi ikon kuliner kaki lima rasa restoran yang akan dikenal di seluruh Indonesia.

## Penutup

Muphus Seafood telah membuktikan bahwa kuliner kaki lima pun dapat memiliki rasa setara restoran, bahkan lebih disukai pelanggan karena kelezatannya yang merakyat. Dengan kualitas menu yang terjaga, harga terjangkau, pelayanan ramah, serta pertumbuhan cabang yang agresif, Muphus Seafood terus membawa cita rasa terbaik ke lebih banyak kota di Indonesia.

Dengan target 60 cabang dan dukungan pelanggan setia, Muphus Seafood layak menjadi salah satu brand kuliner lokal yang patut diperhitungkan dalam dunia kuliner Nusantara.
MD;
    }
}