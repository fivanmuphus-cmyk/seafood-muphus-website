@extends('layouts.app')

@section('title', 'Kemitraan | ' . config('muphus.name'))
@section('description', 'Peluang kemitraan ' . config('muphus.name') . ': spesifikasi lahan, sistem kerja sama, dan alur dari persetujuan lahan sampai grand opening.')

@use('App\Support\Site')

@section('content')
@php
    $c = config('muphus.contact');
    $land = [
        'area' => Site::text('partnership.land_area'),
        'width' => Site::text('partnership.land_width'),
        'depth' => Site::text('partnership.land_depth'),
        'road' => Site::text('partnership.land_road'),
        'status' => Site::text('partnership.land_status'),
    ];
    $wa = 'https://wa.me/' . ($c['partnership_whatsapp'] ?? $c['whatsapp']) . '?text=' . rawurlencode(Site::text('partnership.wa_message'));
    $steps = Site::rows('partnership.steps', ['title', 'text']);
    $img = fn (string $key, string $file) => Site::get('partnership.' . $key) ?: (file_exists(public_path('images/' . $file)) ? $file : null);
@endphp

<x-page-banner page="kemitraan" title="Kemitraan" subtitle="Tumbuh bersama Seafood Muphus di kota Anda." />

{{-- PENGANTAR --}}
<x-section>
    <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-6">
        <div class="lg:col-span-6">
            @if (! in_array(Site::text('partnership.badge'), ['', '-'], true))
                <p class="t-badge mb-6 inline-block bg-crab px-3 py-2 tracking-[.14em] text-white">{{ Site::text('partnership.badge') }}</p>
            @endif
            <x-eyebrow>{{ Site::text('partnership.intro_eyebrow') }}</x-eyebrow>
            <h2 class="t-h2 max-w-[588px]">{{ Site::em('partnership.intro_title', 'text-brand-600') }}</h2>
            <p class="t-body-lg mt-6 max-w-[588px] text-ink-600">{{ Site::text('partnership.intro_text') }}</p>

            <dl class="mt-12 grid max-w-[588px] grid-cols-3 border-b border-line">
                @foreach ([
                    [$branchCount, Site::text('partnership.stat_1_label')],
                    [$land['area'], Site::text('partnership.stat_2_label')],
                    [Site::text('partnership.stat_3_value'), Site::text('partnership.stat_3_label')],
                ] as $n => [$value, $label])
                    <div @class(['py-6 pr-3', 'border-l border-line pl-5' => $n > 0])>
                        <dt class="whitespace-nowrap font-serif text-[34px] leading-[40px] text-brand-600 lg:text-[40px] lg:leading-[44px]">{{ $value }}</dt>
                        <dd class="t-body mt-3 text-ink-600">{{ $label }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="mt-12 flex flex-wrap items-center gap-x-14 gap-y-5">
                <x-button :href="$wa" target="_blank" rel="noopener noreferrer">{{ Site::text('partnership.wa_label') }}</x-button>
                <a href="#lahan" class="t-button border-b border-brand-600 pb-1 text-brand-600">{{ Site::text('partnership.land_link_label') }}</a>
            </div>
        </div>

        <div class="lg:col-span-6">
            <div class="aspect-[4/3] overflow-hidden border border-line lg:aspect-[11/10]">
                @if ($main = $img('img_main', 'kemitraan.jpg'))
                    @if (str_ends_with($main, '.jpg'))
                        <img src="{{ asset('images/' . $main) }}" alt="Outlet Seafood Muphus" loading="lazy" class="h-full w-full object-cover">
                    @else
                        <x-photo :path="$main" alt="Outlet Seafood Muphus" sizes="(min-width:1024px) 588px, 100vw" class="h-full w-full object-cover" />
                    @endif
                @endif
            </div>
        </div>
    </div>

    <x-penghias class="mt-14" />
</x-section>

{{-- SPESIFIKASI LAHAN --}}
<x-section id="lahan" class="pola pola-garis scroll-mt-20">
    <div class="grid gap-12 lg:grid-cols-12 lg:gap-6">
        <div class="lg:col-span-5">
            <x-eyebrow>{{ Site::text('partnership.land_eyebrow') }}</x-eyebrow>
            <h2 class="t-h2">{{ Site::em('partnership.land_title', 'text-brand-600') }}</h2>

            <dl class="mt-10 border-b border-line">
                @foreach ([
                    ['Luas minimal', $land['area']],
                    ['Lebar depan', $land['width']],
                    ['Panjang', $land['depth']],
                    ['Lebar jalan', $land['road']],
                    ['Status lahan', $land['status']],
                ] as [$label, $value])
                    <div class="grid grid-cols-[140px_1fr] items-baseline gap-4 border-t border-line py-5">
                        <dt class="t-eyebrow text-brand-600">{{ $label }}</dt>
                        <dd class="t-h4 text-ink-900">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
            <p class="t-body-sm mt-5 text-ink-600">{{ Site::text('partnership.land_note') }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:col-span-6 lg:col-start-7">
            @foreach ([
                ['img_plan', 'kemitraan-denah.jpg', 'Denah warung', 'col-span-2 bg-white', 'max-h-[460px] object-contain', true],
                ['img_front', 'kemitraan-depan.jpg', 'Tampak depan', 'aspect-[4/3]', 'object-cover', false],
                ['img_inside', 'kemitraan-dalam.jpg', 'Tampak dalam', 'aspect-[4/3]', 'object-cover', false],
            ] as [$key, $file, $caption, $frame, $fit, $natural])
                <figure @class(['relative overflow-hidden border border-line bg-brand-100', $frame])>
                    @if ($shot = $img($key, $file))
                        @php $static = str_ends_with($shot, '.jpg'); @endphp
                        {{-- Diklik: gambar tampil besar (lightbox di layout). --}}
                        <button type="button" data-lightbox="{{ $static ? asset('images/' . $shot) : asset('storage/' . $shot) }}" data-alt="{{ $caption }} outlet Seafood Muphus"
                                class="block w-full {{ $natural ? '' : 'h-full' }} focus-visible:outline-2 focus-visible:outline-offset-[-4px] focus-visible:outline-brand-600"
                                aria-label="Perbesar gambar: {{ $caption }}">
                            @if ($static)
                                <img src="{{ asset('images/' . $shot) }}" alt="{{ $caption }} outlet Seafood Muphus" loading="lazy" class="{{ $natural ? 'h-auto' : 'h-full' }} w-full {{ $fit }}">
                            @else
                                <x-photo :path="$shot" :alt="$caption . ' outlet Seafood Muphus'" sizes="{{ $natural ? '(min-width:1024px) 588px, 100vw' : '(min-width:1024px) 290px, 50vw' }}" class="{{ $natural ? 'h-auto' : 'h-full' }} w-full {{ $fit }}" />
                            @endif
                        </button>
                        <figcaption class="t-badge pointer-events-none absolute bottom-3 left-3 bg-paper px-2 py-1 tracking-[.14em] text-ink-900">{{ $caption }}</figcaption>
                    @endif
                </figure>
            @endforeach
        </div>
    </div>
</x-section>

{{-- LOKASI STRATEGIS --}}
<x-section class="pola pola-laut">
    <x-eyebrow>{{ Site::text('partnership.place_eyebrow') }}</x-eyebrow>
    <h2 class="t-h2">{{ Site::em('partnership.place_title', 'text-brand-600') }}</h2>

    <ol class="mt-12 grid border-t border-line md:grid-cols-3">
        @foreach (Site::rows('partnership.places', ['title', 'text']) as $i => [$title, $text])
            <li class="border-b border-line py-6 md:border-b-0 md:border-l md:px-6 md:first:border-l-0 md:first:pl-0">
                <span class="t-badge tracking-[.14em] text-brand-600">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="t-h4 mt-3 text-brand-600">{{ $title }}</h3>
                <p class="t-body mt-2 max-w-[340px] text-ink-600">{{ $text }}</p>
            </li>
        @endforeach
    </ol>
</x-section>

{{-- ALUR KERJA SAMA --}}
<x-section class="relative isolate overflow-hidden pola pola-kelp bg-brand-900 text-brand-50">
    <x-gelombang :top="true" />
    <x-eyebrow :dark="true">{{ Site::text('partnership.flow_eyebrow') }}</x-eyebrow>
    <h2 class="t-h2 text-white">{{ Site::em('partnership.flow_title', 'text-brand-300') }}</h2>

    <ol class="mt-12 grid border-t border-white/25 md:grid-cols-2 lg:grid-cols-5">
        @foreach ($steps as $i => [$title, $text])
            <li class="border-b border-white/25 py-6 md:px-0 lg:border-l lg:px-5 lg:nth-[5n+1]:border-l-0 lg:nth-[5n+1]:pl-0">
                <span class="t-badge tracking-[.14em] text-brand-300">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="t-h4 mt-3 text-white">{{ $title }}</h3>
                <p class="t-body-sm mt-2 max-w-[260px] text-brand-50">{{ $text }}</p>
            </li>
        @endforeach
    </ol>
    <x-gelombang />
</x-section>

{{-- KENAPA BERMITRA --}}
<x-section class="pola pola-daun">
    <x-eyebrow>{{ Site::text('partnership.why_eyebrow') }}</x-eyebrow>
    <h2 class="t-h2">{{ Site::em('partnership.why_title', 'text-brand-600') }}</h2>

    <ol class="mt-12 grid border-t border-line md:grid-cols-2 lg:grid-cols-4">
        @foreach (Site::rows('partnership.reasons', ['title', 'text']) as $i => [$title, $text])
            <li class="border-b border-line py-6 lg:border-l lg:px-6 lg:first:border-l-0 lg:first:pl-0">
                <span class="t-badge tracking-[.14em] text-brand-600">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="t-h4 mt-3 text-brand-600">{{ $title }}</h3>
                <p class="t-body mt-2 text-ink-600">{{ str_replace('{cabang}', (string) $branchCount, $text) }}</p>
            </li>
        @endforeach
    </ol>
</x-section>

{{-- PERTANYAAN UMUM --}}
@if ($faqs->isNotEmpty())
    <x-section class="pola pola-laut">
        <div class="grid gap-10 lg:grid-cols-12 lg:gap-6">
            <div class="lg:col-span-5">
                <x-eyebrow>{{ Site::text('partnership.faq_eyebrow') }}</x-eyebrow>
                <h2 class="t-h2">{{ Site::em('partnership.faq_title', 'text-brand-600') }}</h2>
                <p class="t-body mt-6 max-w-[400px] text-ink-600">{{ Site::text('partnership.faq_text') }}</p>
                <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer" class="t-button mt-6 inline-block border-b border-brand-600 pb-1 text-brand-600">{{ Site::text('partnership.faq_button') }}</a>
            </div>

            <ul class="lg:col-span-6 lg:col-start-7" data-faq>
                @foreach ($faqs as $i => $faq)
                    <li class="border-t border-line last:border-b" data-faq-item>
                        <button type="button" data-faq-toggle aria-expanded="{{ $i === 0 ? 'true' : 'false' }}"
                                @class(['t-h4 flex w-full items-center justify-between gap-6 py-5 text-left', 'text-brand-600' => $i === 0, 'text-ink-900' => $i !== 0])>
                            <span>{{ $faq->question }}</span>
                            <span class="font-sans text-base text-brand-600" aria-hidden="true" data-faq-icon>{{ $i === 0 ? '×' : '+' }}</span>
                        </button>
                        <div @class(['pb-6', 'hidden' => $i !== 0]) data-faq-panel>
                            <p class="t-body-sm max-w-[520px] text-ink-600">{{ $faq->answer }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </x-section>
@endif

{{-- AJAKAN --}}
<x-section class="relative isolate overflow-hidden pola pola-kelp bg-brand-900 text-brand-50">
    <x-gelombang :top="true" />
    <div class="grid items-end gap-10 lg:grid-cols-12 lg:gap-6">
        <div class="lg:col-span-8">
            <x-eyebrow :dark="true">{{ Site::text('partnership.cta_eyebrow') }}</x-eyebrow>
            <h2 class="t-h2 max-w-[560px] [&_em]:text-brand-300">{{ Site::em('partnership.cta_title') }}</h2>
            <p class="t-body-lg mt-6 max-w-[520px] text-brand-50">{{ Site::text('partnership.cta_text') }}</p>
        </div>
        <div class="flex flex-wrap gap-3 lg:col-span-4 lg:justify-end">
            <x-button :href="$wa" variant="light" target="_blank" rel="noopener noreferrer">{{ Site::text('partnership.cta_wa_label') }}</x-button>
            <x-button href="/kontak?topik=kemitraan#kirim-pesan" variant="outline-light">{{ Site::text('partnership.cta_form_label') }}</x-button>
        </div>
    </div>
</x-section>
@endsection