@extends('layouts.app')

@section('title', config('muphus.name') . ' | Seafood Tenda Kaki Lima Modern')
@section('description', 'Seafood Muphus, seafood tenda kaki lima modern dari Babat, Lamongan. Segar, hangat, untuk keluarga.')

@use('App\Support\Site')

@section('content')
@php
    $stats = config('muphus.stats');
    $heroBanner = \App\Models\PageBanner::query()->where('page', 'beranda')->first();
    $heroImage = $heroBanner?->image_path
        ? asset('storage/' . $heroBanner->image_path)
        : (file_exists(public_path('images/hero.jpg')) ? asset('images/hero.jpg') : null);
    $partnerWa = 'https://wa.me/' . config('muphus.contact.whatsapp') . '?text=' . rawurlencode(Site::text('home.partner_wa_message'));
    $partnerImage = Site::get('home.partner_image');
    $aboutPoints = Site::list('home.about_points');
    // Tampil awal: 3 cabang pertama (urutan admin). Bila pengunjung mengizinkan lokasi, JS menggantinya dengan 3 cabang terdekat.
    $shown = $branches->take(3)->values();
    $firstBranch = $shown->first(fn ($b) => filled($b->mapEmbedUrl()));
    // Cabang yang terbuka di awal: yang pertama punya peta (atau yang pertama bila tidak ada satu pun).
    $openIndex = max(0, (int) $shown->search(fn ($b) => filled($b->mapEmbedUrl())));
@endphp

{{-- 1. HERO --}}
<section class="relative isolate overflow-hidden text-white">
    {{-- Sama dengan Figma: foto tampil penuh, lalu lapisan hijau #0C3414 95% (kiri) ke 38% (kanan), sedikit lebih pekat dari Figma agar teks terbaca, dan pudar ke warna halaman di bawah. --}}
    <div class="absolute inset-0 -z-30 bg-brand-900"></div>
    @if ($heroImage)
        <img src="{{ $heroImage }}" alt="Hidangan seafood {{ config('muphus.name') }}" class="absolute inset-0 -z-20 h-full w-full object-cover" fetchpriority="high" decoding="async">
    @endif
    <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,rgba(12,52,20,.94)_0%,rgba(12,52,20,.78)_100%)] lg:bg-[linear-gradient(90deg,rgba(12,52,20,.95)_0%,rgba(12,52,20,.78)_45%,rgba(12,52,20,.38)_100%)]"></div>
    <div class="absolute inset-x-0 bottom-0 -z-10 h-40 bg-gradient-to-b from-paper/0 to-paper"></div>

    <div class="mx-auto flex min-h-[560px] max-w-[1200px] flex-col justify-center px-5 pb-32 pt-16 md:px-8 lg:min-h-[653px] lg:px-10">
        <p class="t-eyebrow mb-4 flex items-center gap-3 font-normal text-white">
            {{ Site::text('home.hero_eyebrow') }}
        </p>

        <h1 class="t-display max-w-3xl italic [text-shadow:0_2px_16px_rgba(0,0,0,.25)]">
            {{ Site::text('home.hero_title') }}
        </h1>

        <div class="mt-6 max-w-[620px] border-l-[3px] border-sand pl-5 lg:pl-12">
            <p class="t-h4 [text-shadow:0_1px_10px_rgba(0,0,0,.3)]">
                {{ Site::text('home.hero_subtitle') }}
            </p>
        </div>

        <p class="t-h3 mt-5 [text-shadow:0_1px_10px_rgba(0,0,0,.3)]">
            {{ Site::em('home.hero_tagline') }}
        </p>

        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
            <x-button :href="Site::url('home.hero_cta1_url')" class="shadow-lg shadow-black/25">{{ Site::text('home.hero_cta1_label') }}</x-button>
            <x-button :href="Site::url('home.hero_cta2_url')" variant="ghost">{{ Site::text('home.hero_cta2_label') }}</x-button>
        </div>
    </div>
</section>
<div class="h-0.5 bg-[linear-gradient(90deg,#147C24_0%,#D7261E_72%,#5CBC54_100%)]"></div>

{{-- 2. TENTANG --}}
<x-section class="pola pola-daun">
    <div class="grid gap-12 lg:grid-cols-12">
        <div class="lg:col-span-6">
            <x-eyebrow>{{ Site::text('home.about_eyebrow') }}</x-eyebrow>
            <h2 class="t-h2 max-w-[600px]">
                {{ Site::em('home.about_title', 'text-brand-600') }}
            </h2>
            <p class="t-body-lg mt-6 max-w-[580px] text-ink-600">
                {{ Site::text('home.about_text') }}
            </p>
            <a href="/tentang" class="t-button mt-8 inline-block border-b border-brand-600 pb-1 text-brand-600">
                {{ Site::text('home.about_link_label') }}
            </a>
        </div>

        <ol class="lg:col-span-5 lg:col-start-8">
            @foreach ($aboutPoints as $i => $point)
                <li class="grid grid-cols-[44px_1fr] border-t border-line py-7 last:border-b">
                    <span class="t-body-sm pt-1.5 text-ink-600">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <div>
                        <h3 class="t-h4 text-ink-600">{{ $point['title'] }}</h3>
                        <p class="t-body mt-3 leading-[30px] text-ink-600">{{ $point['text'] ?? '' }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>

    <x-penghias class="mt-16" />
</x-section>

{{-- 3. MENU POPULER --}}
<x-section class="relative isolate overflow-hidden pola pola-kelp bg-brand-900 text-brand-50">
    <x-gelombang :top="true" />
    <x-section-head :eyebrow="Site::text('home.menu_eyebrow')" :dark="true" href="/menu" :link-label="Site::text('home.menu_link_label')"
        :text="Site::text('home.menu_text')">
        {{ Site::em('home.menu_title') }}
    </x-section-head>

    @if ($menuItems->isEmpty())
        <p class="border border-dashed border-brand-300/40 p-8 text-center text-sm text-brand-50">Menu akan segera hadir.</p>
    @else
        <x-scroller class="lg:grid-cols-4">
            @foreach ($menuItems as $index => $item)
                <x-menu-card :item="$item" :number="$index + 1" :dark="true" />
            @endforeach
        </x-scroller>
    @endif
    <x-gelombang />
</x-section>

{{-- 4. CABANG --}}
<x-section class="pola pola-laut">
    <x-section-head :eyebrow="Site::text('home.branches_eyebrow')" href="/cabang" :link-label="Site::text('home.branches_link_label')"
        :text="Site::text('home.branches_text')">
        {{ Site::em('home.branches_title') }}
    </x-section-head>

    @if ($branches->isEmpty())
        <p class="border border-dashed border-line p-8 text-center text-sm text-ink-600">Daftar cabang akan segera hadir.</p>
    @else
        <div class="grid gap-8 lg:grid-cols-12" data-branches>
            <div class="border-b border-line lg:col-span-5" data-branch-list>
                <p class="hidden pb-4 text-xs font-medium text-brand-600" data-near-note>Menampilkan 3 cabang terdekat dari lokasi Anda.</p>
                @foreach ($branches as $i => $b)
                    @php $co = $b->coordinates(); @endphp
                    <div @class(['border-t border-line', 'hidden' => $i >= 3]) data-branch @if ($co) data-lat="{{ $co[0] }}" data-lng="{{ $co[1] }}" @endif data-map="{{ $b->mapEmbedUrl() }}" data-name="{{ $b->name }}" data-directions="{{ $b->directionsUrl() }}" data-own-label="{{ $b->hasGoogleEmbed() ? 0 : 1 }}">
                        <button type="button" class="flex w-full items-center gap-5 py-5 text-left" aria-expanded="{{ $i === $openIndex ? 'true' : 'false' }}" data-branch-toggle>
                            <span class="t-body-sm text-ink-600">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="flex-1"><span class="t-h4 block">{{ $b->name }}</span><span class="mt-1 hidden text-xs font-medium text-brand-600" data-distance></span></span>
                            <span class="text-brand-600" aria-hidden="true" data-icon>{{ $i === $openIndex ? '−' : '+' }}</span>
                        </button>
                        <div @class(['space-y-3 pb-6 pl-10', 'hidden' => $i !== $openIndex]) data-branch-panel>
                            @if ($b->openStatus())
                                <p class="flex items-center gap-2 text-xs font-medium text-brand-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>{{ $b->openStatus() }}
                                </p>
                            @endif
                            <p class="t-body-sm text-ink-600">
                                {{ $b->address }}@if ($b->hours_note)<br>{{ $b->hours_note }}@endif
                            </p>
                            <div class="flex flex-wrap items-center gap-4 pt-2">
                                @if ($b->directionsUrl())
                                    <x-button :href="$b->directionsUrl()" variant="dark" target="_blank" rel="noopener noreferrer">Petunjuk arah</x-button>
                                @endif
                                @if ($b->whatsapp)
                                    <a href="tel:+{{ $b->whatsapp }}" class="t-button border-b border-brand-600 pb-1 text-brand-600">Nomor telepon</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="lg:col-span-7">
                <div class="relative aspect-[4/3] overflow-hidden border border-line bg-brand-100 lg:aspect-auto lg:h-full lg:min-h-[380px]">
                    @if ($firstBranch)
                        <iframe data-branch-map src="{{ $firstBranch->mapEmbedUrl() }}" title="Peta lokasi cabang" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade" class="h-full w-full border-0"></iframe>
                    @endif
                    <a data-map-label href="{{ $firstBranch?->directionsUrl() }}" target="_blank" rel="noopener noreferrer"
                       @class(['absolute left-1/2 top-1/2 z-10 ml-4 flex max-w-[60%] -translate-y-[calc(50%+20px)] items-center gap-2 border border-line bg-white px-3 py-2 text-xs font-medium text-ink-900 shadow-sm transition-colors hover:border-brand-600 hover:text-brand-600', 'hidden' => ! ($firstBranch && $firstBranch->directionsUrl() && ! $firstBranch->hasGoogleEmbed())])>
                        <span class="truncate" data-map-label-text>{{ $firstBranch?->name }}</span>
                        <svg viewBox="0 0 12 12" class="h-3 w-3 shrink-0 fill-none stroke-current" stroke-width="1.5" aria-hidden="true"><path d="M3 9 9 3M4.5 3H9v4.5"/></svg>
                    </a>
                    <div data-map-empty @class(['absolute inset-0 flex items-center justify-center bg-brand-100 p-8 text-center text-sm text-ink-600', 'hidden' => (bool) $firstBranch])>
                        {{ $firstBranch ? 'Peta cabang ini belum tersedia. Gunakan tombol Petunjuk arah untuk membuka lokasinya.' : 'Peta akan tampil setelah tautan peta cabang diisi di panel admin.' }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-section>

{{-- 5. ARTIKEL --}}
@if ($articles->isNotEmpty())
    <x-section class="pola pola-laut !pt-0">
        <x-section-head :eyebrow="Site::text('home.articles_eyebrow')" href="/artikel" :link-label="Site::text('home.articles_link_label')"
        :text="Site::text('home.articles_text')">
        {{ Site::em('home.articles_title') }}
    </x-section-head>
        <x-scroller class="lg:grid-cols-3">
            @foreach ($articles as $article)
                <x-article-card :article="$article" />
            @endforeach
        </x-scroller>
    </x-section>
@endif

{{-- 6. KARIR --}}
<x-section class="relative isolate overflow-hidden pola pola-kelp bg-brand-900 text-brand-50">
    <x-gelombang :top="true" />
    <x-section-head :eyebrow="Site::text('home.careers_eyebrow')" :dark="true" href="/karir" :link-label="Site::text('home.careers_link_label')"
        :text="Site::text('home.careers_text')">
        {{ Site::em('home.careers_title') }}
    </x-section-head>

    @if ($jobs->isEmpty())
        <div class="border border-dashed border-brand-50/30 p-8 text-center text-sm leading-6 text-brand-50/80">
            Belum ada lowongan terbuka. Kirim CV Anda ke
            <a href="mailto:{{ config('muphus.contact.hr_email') }}" class="underline">{{ config('muphus.contact.hr_email') }}</a>,
            kami simpan untuk kesempatan berikutnya.
        </div>
    @else
        <x-scroller class="lg:grid-cols-3">
            @foreach ($jobs as $job)
                <x-job-card :job="$job" />
            @endforeach
        </x-scroller>
    @endif
    <x-gelombang />
</x-section>

{{-- 7. KEMITRAAN --}}
<x-section class="pola pola-garis">
    <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-6">
        <div class="lg:col-span-6">
            <x-eyebrow>{{ Site::text('home.partner_eyebrow') }}</x-eyebrow>
            <h2 class="t-h2 max-w-[588px]">
                {{ Site::em('home.partner_title', 'text-brand-600') }}
            </h2>
            <p class="t-body-lg mt-6 max-w-[588px] text-ink-600">
                {{ Site::text('home.partner_text') }}
            </p>

            <dl class="mt-12 grid max-w-[588px] grid-cols-3 border-b border-line">
                @foreach ([
                    [$branchCount, Site::text('home.partner_stat_1')],
                    [$stats['target_outlets'], str_replace('{tahun}', (string) ($stats['target_year'] ?? 2026), Site::text('home.partner_stat_2'))],
                    [$stats['min_land'], Site::text('home.partner_stat_3')],
                ] as $n => [$value, $label])
                    <div @class(['py-6 pr-3', 'border-l border-line pl-5' => $n > 0])>
                        <dt class="whitespace-nowrap font-serif text-[34px] leading-[40px] text-brand-600 lg:text-[40px] lg:leading-[44px]">{{ $value }}</dt>
                        <dd class="t-body mt-3 text-ink-600">{{ $label }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="mt-12 flex flex-wrap items-center gap-x-14 gap-y-5">
                <x-button :href="$partnerWa" target="_blank" rel="noopener noreferrer">{{ Site::text('home.partner_wa_label') }}</x-button>
                <a href="/kemitraan" class="t-button border-b border-brand-600 pb-1 text-brand-600">{{ Site::text('home.partner_link_label') }}</a>
            </div>
        </div>

        <div class="lg:col-span-6">
            <div class="aspect-[4/3] overflow-hidden border border-line lg:aspect-[11/10]">
                @if ($partnerImage)
                    <x-photo :path="$partnerImage" alt="Outlet Seafood Muphus" sizes="(min-width:1024px) 588px, 100vw" class="h-full w-full object-cover" />
                @elseif (file_exists(public_path('images/kemitraan.jpg')))
                    <img src="{{ asset('images/kemitraan.jpg') }}" alt="Outlet Seafood Muphus" loading="lazy" class="h-full w-full object-cover">
                @endif
            </div>
        </div>
    </div>
</x-section>
@endsection