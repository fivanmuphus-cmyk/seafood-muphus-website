<?php

return [
    'name' => 'Seafood Muphus',
    'tagline' => 'Segar, hangat, untuk keluarga.',
    'company' => 'PT. Indonesia Menembus Gravitasi',

    'contact' => [
        'whatsapp' => '628113076889',
        'whatsapp_display' => '0811 3076 889',
        'partnership_whatsapp' => '6281130878783',
        'partnership_whatsapp_display' => '0811 3087 8783',
        'hr_whatsapp' => '628113049145',
        'hr_whatsapp_display' => '0811 3049 145',
        'hr_instagram' => 'lokerseafoodmuphus',
        'email' => 'indonesiamenembusgravitasi2@gmail.com',
        'hr_email' => 'hrdmuphusseafood@gmail.com',
        'address' => 'Jl. Ruko Merak Blok A-03, Kec. Merakurak, Kab. Tuban, Jawa Timur',
    ],

    // ID pengukuran Google Analytics 4 (bentuknya G-XXXXXXXXXX). Diisi lewat admin: Pengaturan Umum > Statistik pengunjung.
    'analytics' => ['id' => null],

    // Kosongkan (null) bila akunnya belum ada. Yang kosong tidak ditampilkan.
    // Akun lain bisa ditambah di admin: Pengaturan Situs > Pengaturan Umum > Media sosial lainnya.
    'social' => [
        'instagram' => 'https://www.instagram.com/seafoodmuphusofficial/',
        'facebook' => 'https://www.facebook.com/share/1DdWNVt2rn/',
        'tiktok' => 'https://www.tiktok.com/@seafoodmuphusofficial',
        'threads' => 'https://www.threads.com/@seafoodmuphusofficial',
        'youtube' => 'https://www.youtube.com/@seafoodmuphusofficial',
    ],

    'stats' => [
        'branches' => 31,
        'target_outlets' => 63,
        'founded' => 2022,
        'min_land' => '1.250 m²',
        'target_year' => 2026,
    ],

    'nav' => [
        ['Beranda', '/'],
        ['Tentang', '/tentang'],
        ['Menu', '/menu'],
        ['Cabang', '/cabang'],
        ['Artikel', '/artikel'],
        ['Karir', '/karir'],
        ['Kemitraan', '/kemitraan'],
    ],

        // Topik di formulir Kontak (kunci => label)
    'contact_topics' => [
        'menu' => 'Informasi menu',
        'kemitraan' => 'Kemitraan',
        'karir' => 'Karir',
        'saran' => 'Saran dan keluhan',
        'lainnya' => 'Lainnya',
    ],

        // Spesifikasi lahan kemitraan (dari flyer Expand)
    'land_spec' => [
        'area' => '1.250 m²',
        'width' => '25 m',
        'depth' => '50 m',
        'road' => '± 7–14 m',
        'status' => 'Sewa / Kerja sama',
    ],

        // Kategori dan subkategori menu (kunci => nama tampilan). Urutan di sini = urutan tombol.
    'menu_categories' => [
        'seafood' => 'Seafood',
        'paket' => 'Paket keluarga',
        'ayam-bebek' => 'Ayam dan bebek',
        'sayur' => 'Sayur',
        'pelengkap' => 'Pelengkap',
        'minuman' => 'Minuman',
    ],
    'menu_subcategories' => [
        'mix-spesial' => 'Mix spesial',
        'ikan' => 'Ikan',
        'kepiting-lobster' => 'Kepiting dan lobster',
        'cumi-udang-kerang' => 'Cumi, udang, kerang',
    ],

    // Isi hanya bila jam layanan sudah pasti, contoh: 'Setiap hari, 10.00–22.00'. null = tidak ditampilkan.
    'service_hours' => null,
];