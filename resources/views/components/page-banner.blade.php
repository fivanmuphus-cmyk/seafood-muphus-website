@props(['page' => null, 'title' => '', 'subtitle' => null, 'image' => null])
@php
    // Foto, judul, dan subjudul bisa diganti dari admin (menu "Banner halaman").
    // Latar hijau tua; foto ditutup gradasi hijau dari kiri (90%) ke kanan (10%).
    $banner = $page ? \App\Models\PageBanner::query()->where('page', $page)->first() : null;
    $heading = $banner?->title ?: $title;
    $sub = $banner?->subtitle ?: $subtitle;
    $src = $banner?->image_path
        ? asset('storage/' . $banner->image_path)
        : (($image && file_exists(public_path($image))) ? asset($image) : null);
@endphp
<section class="relative isolate overflow-hidden bg-brand-900">
    @if ($src)
        <img src="{{ $src }}" alt="{{ strip_tags($heading) }} | {{ config('muphus.name') }}" class="absolute inset-0 -z-30 h-full w-full object-cover saturate-[.85]" fetchpriority="high" decoding="async">
        {{-- Gradasi kiri ke kanan: 90% di kiri, 10% di kanan. --}}
        <div class="absolute inset-0 -z-20 bg-gradient-to-r from-brand-900/90 to-brand-900/10"></div>
    @endif

    <div class="mx-auto max-w-[1200px] px-5 pb-24 pt-20 md:px-8 md:pb-28 md:pt-24 lg:flex lg:min-h-[380px] lg:flex-col lg:justify-center lg:px-10 lg:pb-28 lg:pt-24">
        @php
            // Kata terakhir judul dimiringkan dan diberi hijau muda.
            $words = preg_split('/\s+/', trim($heading));
            $last = count($words) > 1 ? array_pop($words) : null;
        @endphp
        <h1 class="t-h1 text-white">@if ($last){{ implode(' ', $words) }} <em class="text-brand-200">{{ $last }}</em>@else{{ $heading }}@endif</h1>
        @if ($sub)
            <p class="t-h4 mt-3 font-serif italic text-brand-50">{{ $sub }}</p>
        @endif
    </div>

    {{-- Gelombang bertingkat di tepi bawah: hijau tua (banner) turun bertahap lewat hijau sedang dan hijau pucat sampai paper. --}}
    <svg viewBox="0 0 1440 120" preserveAspectRatio="none" class="pointer-events-none absolute inset-x-0 bottom-0 -z-[5] block h-14 w-full md:h-20 lg:h-28" aria-hidden="true">
        <path class="fill-brand-600 opacity-50" d="M0 40 C260 8 520 24 780 52 C1020 78 1240 66 1440 22 L1440 121 L0 121 Z" />
        <path class="fill-brand-300 opacity-40" d="M0 58 C240 26 500 44 760 68 C1000 90 1230 82 1440 46 L1440 121 L0 121 Z" />
        <path class="fill-brand-100" d="M0 76 C250 50 500 62 760 82 C1010 100 1230 92 1440 66 L1440 121 L0 121 Z" />
        <path class="fill-none stroke-brand-200" stroke-width="1.5" vector-effect="non-scaling-stroke" d="M0 84 C260 60 500 72 760 90 C1010 106 1230 98 1440 76" />
        <path class="fill-paper" d="M0 96 C260 76 520 86 780 100 C1020 112 1240 108 1440 90 L1440 121 L0 121 Z" />
    </svg>
</section>