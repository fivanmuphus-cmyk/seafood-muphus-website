@extends('layouts.app')

@section('title', 'Tentang Muphus | ' . config('muphus.name'))
@section('description', 'PT. Indonesia Menembus Gravitasi (Seafood Muphus): usaha kuliner dengan konsep kaki lima modern, berawal di Babat, Lamongan pada 2022.')

@use('App\Support\Site')

@section('content')
@php
    $stats = config('muphus.stats');
    $target = $stats['target_outlets'];
    $year = $stats['target_year'] ?? 2026;
    $vars = ['cabang' => $branchCount, 'tahun' => $year, 'target' => $target];
    $fill = fn (string $t) => Site::fill($t, $vars);
    $img = fn (string $key, string $file) => Site::get('about.' . $key) ?: (file_exists(public_path('images/' . $file)) ? $file : null);
@endphp

<x-page-banner page="tentang" title="Tentang Muphus" subtitle="PT. Indonesia Menembus Gravitasi (Seafood Muphus)" image="images/banner-tentang.jpg" />

{{-- SEKILAS PROFIL: foto lebar di atas, lalu judul dan teks ditumpuk ke bawah --}}
<x-section class="crab-bg">
    {{-- Foto lebar di atas teks, berbingkai garis ganda --}}
    @if ($photo = $img('img_1', 'tentang-1.jpg'))
        <div class="mb-12 border border-line bg-paper p-2 sm:p-3 lg:mb-16">
            <div class="aspect-[4/3] overflow-hidden border border-line bg-paper sm:aspect-[16/9] lg:aspect-[21/9]">
                @if (str_ends_with($photo, '.jpg'))
                    <img src="{{ asset('images/' . $photo) }}" alt="Tim Seafood Muphus" loading="lazy" class="h-full w-full object-cover">
                @else
                    <x-photo :path="$photo" alt="Tim Seafood Muphus" sizes="(min-width:1200px) 1120px, 100vw" class="h-full w-full object-cover" />
                @endif
            </div>
        </div>
    @endif

    <div class="relative mb-12 border-x border-line px-6 py-4 sm:px-10 lg:mb-16 lg:px-14 lg:py-6">
        {{-- Logo kepiting: tepat di tengah blok teks, sangat samar --}}
        <img src="{{ asset('images/logo-kepiting.svg') }}" alt="" aria-hidden="true" width="561" height="470"
             class="pointer-events-none absolute left-1/2 top-1/2 -z-10 w-[70%] max-w-[520px] -translate-x-1/2 -translate-y-1/2 select-none opacity-[.05]">

        {{-- Judul di atas, isi di bawah (satu kolom), di dalam garis tegak tipis kanan-kiri --}}
        <div class="mx-auto max-w-[860px]">
            <x-eyebrow>{{ Site::text('about.profile_eyebrow') }}</x-eyebrow>
            <p class="t-body-lg text-ink-600">{{ Site::text('about.profile_company') }}</p>
            <h2 class="t-h2 mt-6 max-w-[760px]">{{ Site::em('about.profile_title', 'text-brand-600') }}</h2>

            <div class="t-body-lg mt-10 space-y-[30px] text-ink-600">
                @foreach (Site::rows('about.profile_paragraphs', ['text']) as [$paragraph])
                    <p>{!! nl2br(e($fill($paragraph))) !!}</p>
                @endforeach
            </div>

            <dl class="mt-10 border-y border-line">
                @foreach (Site::rows('about.facts', ['title', 'text']) as [$label, $value])
                    <div class="flex items-baseline gap-6 py-4 first:border-b first:border-line">
                        <dt class="t-badge w-[130px] shrink-0 tracking-[.14em] text-brand-600">{{ $label }}</dt>
                        <dd class="t-body-sm text-ink-600">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>

    <dl class="grid border-y border-line sm:grid-cols-3">
        @foreach ([
            [$branchCount, Site::text('about.stat_1_label')],
            [$stats['founded'], Site::text('about.stat_2_label')],
            [$target, $fill(Site::text('about.stat_3_label'))],
        ] as $i => [$value, $label])
            <div @class(['py-8 sm:px-8', 'sm:border-l sm:border-line' => $i > 0, 'sm:pl-0' => $i === 0])>
                <dt class="t-stat text-brand-600">{{ $value }}</dt>
                <dd class="t-badge mt-3 font-normal tracking-[.14em] text-ink-600">{{ $label }}</dd>
            </div>
        @endforeach
    </dl>

    <x-penghias class="mt-14" />
</x-section>

{{-- PERJALANAN KAMI --}}
<x-section class="relative isolate overflow-hidden pola pola-kelp bg-brand-900 text-brand-50">
    <x-gelombang :top="true" />
    <x-eyebrow :dark="true">{{ Site::text('about.journey_eyebrow') }}</x-eyebrow>
    <h2 class="t-h2">{{ Site::em('about.journey_title', 'text-brand-300') }}</h2>
    @php $journey = Site::rows('about.journey', ['label', 'title', 'text']); @endphp

    <ol @class(['mt-14 lg:grid lg:gap-6', 'lg:grid-cols-3' => count($journey) <= 3, 'lg:grid-cols-4' => count($journey) === 4, 'lg:grid-cols-5' => count($journey) >= 5])>
        @foreach ($journey as $i => [$label, $title, $text])
            <li class="relative pb-10 pl-10 last:pb-0 lg:pb-0 lg:pl-0 lg:pt-12">
                <span @class(['absolute left-0 top-1 h-5 w-5 rounded-full lg:top-0', 'bg-brand-300' => $i < count($journey) - 1, 'bg-crab' => $i === count($journey) - 1])></span>
                <span class="absolute bottom-0 left-[9px] top-7 w-px bg-white/25 last:hidden lg:bottom-auto lg:left-8 lg:right-2 lg:top-[9px] lg:h-px lg:w-auto"></span>
                <p class="t-badge font-normal tracking-[.14em] text-brand-300">{{ $fill($label) }}</p>
                <h3 class="t-h4 mt-3 max-w-[220px] text-white">{{ $fill($title) }}</h3>
                <p class="t-body-sm mt-3 max-w-[240px] text-brand-50">{{ $fill($text) }}</p>
            </li>
        @endforeach
    </ol>
    <x-gelombang />
</x-section>

{{-- STANDAR KAMI --}}
<x-section class="pola pola-laut">
    <x-eyebrow>{{ Site::text('about.standards_eyebrow') }}</x-eyebrow>
    <h2 class="t-h2">{{ Site::em('about.standards_title', 'text-brand-600') }}</h2>

    <ol class="mt-12 grid border-b border-line sm:grid-cols-2 lg:grid-cols-4">
        @foreach (Site::rows('about.standards', ['title', 'text']) as $i => [$title, $text])
            <li class="py-6 lg:border-l lg:border-line lg:px-6 lg:first:border-l-0 lg:first:pl-0">
                <span class="t-badge tracking-[.14em] text-brand-600">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="t-h4 mt-3 text-brand-600">{{ $title }}</h3>
                <p class="t-body mt-2 text-ink-600">{{ $text }}</p>
            </li>
        @endforeach
    </ol>

    <p class="t-body-lg mt-6 border-l-[3px] border-crab pl-5 font-serif italic text-ink-600">
        {{ Site::text('about.standards_note') }}
    </p>
</x-section>

{{-- ARAH KAMI --}}
<x-section class="pola pola-seafood !pt-0">
    <x-eyebrow>{{ Site::text('about.direction_eyebrow') }}</x-eyebrow>
    <h2 class="t-h2 [&_em]:text-brand-600">{{ Site::em('about.direction_title') }}</h2>

    <div class="mt-12 grid gap-12 border-t border-line pt-10 lg:grid-cols-12 lg:gap-6">
        <div class="lg:col-span-6">
            <p class="t-eyebrow text-brand-600">{{ Site::text('about.vision_label') }}</p>
            <p class="t-h3 mt-10 max-w-[420px] [&_em]:text-brand-600">{{ Site::em('about.vision_text') }}</p>
        </div>
        <div class="lg:col-span-6">
            <p class="t-eyebrow text-brand-600">{{ Site::text('about.mission_label') }}</p>
            <ol class="mt-10">
                @foreach (Site::rows('about.missions', ['text']) as $i => [$text])
                    <li class="grid grid-cols-[36px_1fr] border-b border-line py-5 first:pt-0">
                        <span class="t-badge pt-1 tracking-[.14em] text-brand-600">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <p class="t-body text-ink-600">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>

    <div class="mt-16">
        <p class="t-eyebrow text-brand-600">{{ Site::text('about.value_eyebrow') }}</p>
        <h3 class="t-h2 mt-3">{{ Site::text('about.value_title') }}</h3>
        <p class="t-body mt-2 text-ink-600">{{ Site::text('about.value_text') }}</p>

        <ol class="mt-10">
            @foreach (Site::rows('about.values', ['letter', 'title', 'text']) as [$letter, $name, $text])
                <li class="grid grid-cols-[64px_1fr] pt-7 lg:grid-cols-[100px_1fr]">
                    <span class="font-serif text-[44px] leading-none text-brand-600 lg:text-[52px]">{{ $letter }}</span>
                    <div class="border-b border-line pb-7">
                        <h4 class="t-h4 uppercase text-brand-600">{{ $name }}</h4>
                        <p class="t-body mt-2 text-ink-600">{{ $text }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</x-section>

{{-- AJAKAN --}}
<x-section class="relative isolate overflow-hidden bg-brand-900 text-brand-50">
    <x-gelombang :top="true" />
    <div class="grid items-end gap-10 lg:grid-cols-12 lg:gap-6">
        <div class="lg:col-span-8">
            <x-eyebrow :dark="true">{{ Site::text('about.cta_eyebrow') }}</x-eyebrow>
            <h2 class="t-h2 max-w-[560px] [&_em]:text-brand-300">{{ Site::em('about.cta_title') }}</h2>
            <p class="t-body-lg mt-6 max-w-[520px] text-brand-50">{{ $fill(Site::text('about.cta_text')) }}</p>
        </div>
        <div class="flex flex-wrap gap-3 lg:col-span-4 lg:justify-end">
            <x-button href="/cabang" variant="light">{{ Site::text('about.cta_1_label') }}</x-button>
            <x-button href="/karir" variant="outline-light">{{ Site::text('about.cta_2_label') }}</x-button>
        </div>
    </div>
</x-section>
@endsection