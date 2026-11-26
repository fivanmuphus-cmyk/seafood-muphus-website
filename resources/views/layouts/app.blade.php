<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @include('partials.seo')

    {{-- Layar muat hanya dipakai bila belum pernah tampil di sesi ini dan perangkat tidak meminta "kurangi gerakan". --}}
    <script>
        (function () {
            try {
                var seen = sessionStorage.getItem('muphus:splash');
                var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                if (!seen && !reduce) document.documentElement.classList.add('has-preloader');
            } catch (e) {}
        })();
    </script>
    <noscript><style>#preloader { display: none !important; }</style></noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Bagian yang tadinya putih polos dibiarkan transparan agar warna kertas (di app.css) terlihat. */
        .site-bg > section.bg-white { background-color: transparent; }

        /* Alamat gambar motif ditulis di sini (lewat asset()) agar benar saat npm run dev maupun build. */
        .pola-seafood { --pola: url('{{ asset('images/pola/seafood.webp') }}'); }
        .pola-kelp    { --pola: url('{{ asset('images/pola/kelp.webp') }}'); }
        .pola-daun    { --pola: url('{{ asset('images/pola/daun.webp') }}'); }
        .pola-garis   { --pola: url('{{ asset('images/pola/garis-daun.webp') }}'); }
        .pola-laut    { --pola: url('{{ asset('images/pola/laut.webp') }}'); }
        /* Kursor logo kepiting (titik tunjuk di tengah gambar); kolom isian tetap kursor teks. */
        :root { --kursor: url('{{ asset('images/kursor-kepiting.svg') }}'); }
        html, body *:not(input):not(textarea):not(select) { cursor: var(--kursor) 22 18, auto; }
        /* Yang bisa diklik memakai kursor tangan (pointer), termasuk isi di dalamnya seperti ikon dan teks. */
        :is(a[href], button, summary, label[for], select, [role='button'], [data-lightbox]),
        :is(a[href], button, summary, label[for], select, [role='button'], [data-lightbox]) * { cursor: pointer; }
        :is(button, select):disabled, :is(button, select):disabled * { cursor: not-allowed; }
    </style>
</head>
<body class="bg-paper font-sans text-ink-900 antialiased">
    <div id="nav-progress" aria-hidden="true"></div>
    @include('partials.preloader')

    @include('partials.navbar')
    <main class="site-bg">@yield('content')</main>
    @include('partials.footer')

    <dialog id="lightbox" class="m-auto max-h-[92vh] max-w-[92vw] bg-transparent p-0 backdrop:bg-black/80">
        <form method="dialog" class="absolute right-2 top-2 z-10">
            <button class="bg-paper px-3 py-2 text-xs font-medium uppercase tracking-[.14em]">Tutup</button>
        </form>
        <img id="lightbox-img" src="" alt="" class="max-h-[92vh] max-w-[92vw] object-contain">
    </dialog>

    @include('partials.analytics')
</body>
</html>