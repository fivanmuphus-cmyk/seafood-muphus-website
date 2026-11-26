@extends('layouts.app')

@section('title', 'Kebijakan Privasi | ' . config('muphus.name'))
@section('description', 'Cara ' . config('muphus.name') . ' mengumpulkan, memakai, dan melindungi data pribadi pengunjung website.')

@section('content')
@php
    $email = config('muphus.contact.email');
    $hasAnalytics = (bool) preg_match('/^G-[A-Z0-9]{4,15}$/', (string) config('muphus.analytics.id'));
@endphp

<x-section>
    <div class="mx-auto max-w-[720px]">
        <x-eyebrow>Kebijakan Privasi</x-eyebrow>
        <h1 class="t-h2 mt-4">Cara kami menjaga data Anda</h1>
        <p class="t-body-sm mt-4 text-ink-600">Terakhir diperbarui: 25 November 2026</p>

        <div class="mt-10 space-y-10 text-base leading-7 text-ink-700">
            <section>
                <h2 class="t-h4 mb-3 text-ink-900">Siapa kami</h2>
                <p>Website ini dikelola oleh {{ config('muphus.company') }}, pemilik merek {{ config('muphus.name') }}. Kebijakan ini menjelaskan data apa yang kami kumpulkan saat Anda memakai website ini, untuk apa, dan hak Anda atas data tersebut.</p>
            </section>

            <section>
                <h2 class="t-h4 mb-3 text-ink-900">Data yang kami kumpulkan</h2>
                <ul class="list-disc space-y-2 pl-5">
                    <li><strong>Formulir kontak.</strong> Nama, email, nomor telepon (bila diisi), topik, dan isi pesan yang Anda kirim.</li>
                    <li><strong>WhatsApp dan email.</strong> Bila Anda menghubungi kami lewat WhatsApp atau email, kami menerima nomor atau alamat email Anda dan isi percakapan.</li>
                    <li><strong>Lokasi perangkat.</strong> Beranda dapat meminta izin lokasi untuk menampilkan cabang terdekat. Lokasi diolah di browser Anda saja, tidak dikirim dan tidak disimpan di server kami. Anda boleh menolak, dan website tetap dapat dipakai.</li>
                    @if ($hasAnalytics)
                        <li><strong>Statistik kunjungan.</strong> Bila Anda menekan Terima pada banner cookie, kami memakai Google Analytics untuk mencatat halaman yang dibuka, jenis perangkat, dan perkiraan wilayah secara agregat. Alamat IP dianonimkan.</li>
                    @endif
                </ul>
            </section>

            <section>
                <h2 class="t-h4 mb-3 text-ink-900">Untuk apa data dipakai</h2>
                <p>Data dari formulir dan pesan dipakai untuk menjawab pertanyaan, menindaklanjuti kemitraan atau lamaran, dan menghubungi Anda kembali sesuai permintaan. Kami tidak menjual data pribadi Anda dan tidak memakainya untuk iklan pihak lain.</p>
            </section>

            <section>
                <h2 class="t-h4 mb-3 text-ink-900">Cookie</h2>
                <p>Website ini memakai cookie teknis yang diperlukan agar formulir dan keamanan berjalan, misalnya cookie sesi. Cookie ini tidak melacak Anda dan tidak membutuhkan persetujuan.</p>
                @if ($hasAnalytics)
                    <p class="mt-3">Cookie analitik dari Google hanya dipasang setelah Anda setuju. Pilihan Anda tersimpan di browser dan dapat diubah kapan saja lewat tautan "Pengaturan cookie" di bagian bawah halaman.</p>
                @else
                    <p class="mt-3">Saat ini kami tidak memakai cookie analitik maupun iklan.</p>
                @endif
            </section>

            <section>
                <h2 class="t-h4 mb-3 text-ink-900">Berapa lama data disimpan</h2>
                <p>Pesan Anda kami simpan selama masih diperlukan untuk menanggapi dan menindaklanjutinya, lalu dihapus atau dianonimkan bila sudah tidak diperlukan.</p>
            </section>

            <section>
                <h2 class="t-h4 mb-3 text-ink-900">Hak Anda</h2>
                <p>Sesuai UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi, Anda dapat meminta akses, perbaikan, atau penghapusan data pribadi Anda, serta menarik persetujuan yang pernah diberikan. Kirim permintaan ke <a href="mailto:{{ $email }}" class="text-brand-600 underline">{{ $email }}</a>.</p>
            </section>

            <section>
                <h2 class="t-h4 mb-3 text-ink-900">Perubahan kebijakan</h2>
                <p>Bila kebijakan ini berubah, versi terbaru akan tampil di halaman ini beserta tanggal pembaruannya.</p>
            </section>
        </div>
    </div>
</x-section>
@endsection
