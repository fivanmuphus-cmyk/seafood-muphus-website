{{-- Layar muat: tampil sekali per sesi, hilang otomatis setelah halaman siap (lihat app.js dan app.css). --}}
<div id="preloader" role="status" aria-live="polite">
    @if (file_exists(public_path('images/logo.webp')))
        <img src="{{ asset('images/logo.webp') }}" alt="" width="226" height="56" class="preloader-logo" fetchpriority="high">
    @else
        <span class="preloader-logo font-serif text-3xl text-brand-600">{{ config('muphus.name') }}</span>
    @endif
    <span class="preloader-bar" aria-hidden="true"></span>
    <span class="sr-only">Memuat halaman…</span>
</div>