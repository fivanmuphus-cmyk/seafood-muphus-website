@extends('layouts.app')

@section('title', 'Menu Muphus | ' . config('muphus.name'))
@section('description', 'Deretan menu Seafood Muphus: aneka seafood, paket keluarga, ayam, bebek, sayur, dan minuman dengan harga terjangkau untuk semua kalangan.')

@use('App\Support\Site')

@section('content')
@php
    $chip = 't-button border border-line bg-white px-6 py-4 text-ink-600 transition-colors hover:border-brand-600 aria-pressed:border-brand-600 aria-pressed:bg-brand-600 aria-pressed:text-white';
    $fill = fn (string $t) => Site::fill($t, ['cabang' => $branchCount]);
    $hasAny = $cards->isNotEmpty() || $list->isNotEmpty();
@endphp

{{-- Ikon cabai (dipakai berulang lewat <use>, tanpa berkas tambahan) --}}
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
    <symbol id="icon-chili" viewBox="0 0 58 66">
        <path d="M16.5 58C9.8586 59.1069 4.90242 62.6117 0 63C10.3273 63.2791 17.5 67.5 28 63C34.7845 60.7317 40.7085 55.7113 45 52C51.2789 46.57 53.856 39.9871 56 34C57.445 29.9648 56.8903 24.6447 57 21.5001C56.1797 21.295 54.9919 22.0622 54 22C53.1561 21.9471 52.3575 20.9811 51.5 21C50.5687 21.0206 49.8222 21.9304 49 22C47.998 22.0848 47.1254 21.4039 46.5 21.5001C46.7235 23.5956 45.1107 27.5943 45 29.5C44.763 33.581 43.5137 35.7353 42 39C39.4148 44.5757 36.432 48.0146 31.5 51.5C26.9849 54.6908 22.5 57 16.5 58Z" fill="#D7261E"/>
        <path d="M57 21.5001C57.6667 20.5 58 18 56 17C55.5528 16.7764 54.7461 16.377 53 16C53 7.5 27 10.5 26 0H24.5C25.5 11.5 53 9.5 50 16C48 16 44.5 17.1001 46.5 21.5001C47.1254 21.4039 47.998 22.0848 49 22C49.8222 21.9304 50.5687 21.0206 51.5 21C52.3575 20.9811 53.1561 21.9471 54 22C54.9919 22.0622 56.1797 21.295 57 21.5001Z" fill="#2F7D1E"/>
    </symbol>
</svg>

<x-page-banner page="menu" title="Menu Muphus" subtitle="Deretan menu favorit Seafood Muphus" />

<x-section data-menu-page>
    <div class="grid gap-10 lg:grid-cols-12 lg:gap-6">
        <div class="lg:col-span-6">
            <x-eyebrow>{{ Site::text('menu.intro_eyebrow') }}</x-eyebrow>
            <p class="t-body-lg text-ink-600">{{ Site::text('menu.intro_company') }}</p>
            <h2 class="t-h2 mt-6 max-w-[520px]">{{ Site::em('menu.intro_title', 'text-brand-600') }}</h2>
        </div>
        <p class="t-body-lg max-w-[588px] text-ink-600 lg:col-span-6 lg:col-start-7 lg:pt-1">{{ Site::text('menu.intro_text') }}</p>
    </div>

    @if (! $hasAny)
        <p class="mt-12 border border-dashed border-line p-8 text-center text-sm text-ink-600">Menu akan segera hadir.</p>
    @else
        {{-- Saring: kategori, lalu subkategori (khusus Seafood) --}}
        @if ($categories->count() > 1)
            <div class="mt-12 flex flex-wrap gap-3" role="group" aria-label="Saring menu berdasarkan kategori">
                <button type="button" data-cat="all" aria-pressed="true" class="{{ $chip }}">Semua</button>
                @foreach ($categories as $key => $label)
                    <button type="button" data-cat="{{ $key }}" aria-pressed="false" class="{{ $chip }}">{{ $label }}</button>
                @endforeach
            </div>

            @if ($subcategories->count() > 1)
                <div class="mt-3 hidden flex-wrap gap-3 border-l-2 border-brand-600 pl-4" data-sub-bar role="group" aria-label="Saring Seafood berdasarkan jenis">
                    <button type="button" data-sub-filter="all" aria-pressed="true" class="{{ $chip }} !py-3">Semua seafood</button>
                    @foreach ($subcategories as $key => $label)
                        <button type="button" data-sub-filter="{{ $key }}" aria-pressed="false" class="{{ $chip }} !py-3">{{ $label }}</button>
                    @endforeach
                </div>
            @endif
        @endif

        {{-- MENU ANDALAN (kartu bergambar) --}}
        @if ($cards->isNotEmpty())
            <div class="mt-14" data-section="andalan">
                <h3 class="t-h3">{{ Site::text('menu.featured_title') }}</h3>
                <div class="mt-8 grid grid-cols-2 gap-x-4 gap-y-12 lg:grid-cols-4 lg:gap-x-6">
                    @foreach ($cards as $index => $item)
                        <div data-item data-kind="card" data-category="{{ $item->category }}" data-sub="{{ $item->subcategory }}">
                            <x-menu-card :item="$item" :number="$index + 1" :show-category="true" />
                        </div>
                    @endforeach
                </div>
                <div class="mt-12 text-center">
                    <button type="button" data-more-cards class="t-button border-b border-brand-600 pb-1 text-brand-600">{{ Site::text('menu.more_cards_label') }}</button>
                </div>
            </div>
        @endif

        {{-- DAFTAR MENU LENGKAP --}}
        @if ($list->isNotEmpty())
            <div class="mt-20 scroll-mt-24" id="daftar-lengkap" data-section="daftar">
                <h3 class="t-h3">{{ Site::text('menu.list_title') }}</h3>
                <p class="t-body-sm mt-3 text-ink-600">
                    {{ Site::text('menu.list_note') }}
                    <span class="ml-2 inline-flex items-center gap-1.5"><svg width="14" height="16" viewBox="0 0 58 66" aria-hidden="true"><use href="#icon-chili"/></svg> {{ Site::text('menu.spicy_note') }}</span>
                </p>

                <div class="mt-8 grid items-start gap-4 md:grid-cols-2 lg:grid-cols-3 lg:gap-6">
                    @foreach ($list as $group)
                        <details class="group border border-line bg-white" data-item data-kind="list" data-detail
                                 data-category="{{ $group->category }}" data-sub="{{ $group->subcategory }}">
                            <summary class="flex cursor-pointer list-none items-center gap-4 p-4 sm:p-5 [&::-webkit-details-marker]:hidden">
                                <span class="relative h-20 w-20 shrink-0 overflow-hidden border border-line bg-paper sm:h-24 sm:w-24">
                                    @if ($group->image_path)
                                        <x-photo :path="$group->image_path" :alt="$group->name" sizes="96px" class="h-full w-full object-cover" />
                                    @else
                                        <span class="flex h-full w-full items-center justify-center text-brand-300" aria-hidden="true">
                                            <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="5"/></svg>
                                        </span>
                                    @endif
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="t-h4 block text-brand-600">{{ $group->name }}</span>
                                    @if ($group->priceLabel())
                                        <span class="t-body-sm mt-1 block text-ink-900">{{ $group->priceLabel() }}</span>
                                    @endif
                                    @if ($group->description)
                                        <span class="t-body-sm mt-1 block text-ink-600">{{ $group->description }}</span>
                                    @endif
                                </span>
                                <span class="text-brand-600 group-open:hidden" aria-hidden="true">+</span>
                                <span class="hidden text-brand-600 group-open:inline" aria-hidden="true">−</span>
                            </summary>

                            @if (! empty($group->variants))
                                <ul class="divide-y divide-line/70 border-t border-line px-6 py-3">
                                    @foreach ($group->variants as $variant)
                                        <li class="flex items-start justify-between gap-4 py-2.5">
                                            <span class="t-body-sm text-ink-900">
                                                {{ $variant['name'] }}
                                                @if (! empty($variant['spicy']))
                                                    <svg width="14" height="16" viewBox="0 0 58 66" class="ml-1 inline-block" style="vertical-align:-2px" aria-hidden="true"><use href="#icon-chili"/></svg><span class="sr-only">(pedas)</span>
                                                @endif
                                                @if (! empty($variant['note']))
                                                    <span class="mt-0.5 block text-ink-600">{{ $variant['note'] }}</span>
                                                @endif
                                            </span>
                                            @if (! empty($variant['price']))
                                                <span class="t-body-sm shrink-0 font-medium text-ink-900">{{ \App\Models\MenuItem::shortPrice((int) $variant['price']) }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </details>
                    @endforeach
                </div>
            </div>
        @endif

        <p class="mt-12 hidden border border-dashed border-line p-8 text-center text-sm text-ink-600" data-empty>Belum ada menu di kategori ini.</p>
    @endif
</x-section>

{{-- CARA MENIKMATI --}}
<x-section class="pola pola-daun">
    <x-eyebrow>{{ Site::text('menu.ways_eyebrow') }}</x-eyebrow>
    <h2 class="t-h2">{{ Site::em('menu.ways_title', 'text-brand-600') }}</h2>

    <ol class="mt-12 grid border-t border-line md:grid-cols-3">
        @foreach (Site::rows('menu.ways', ['title', 'text']) as $i => [$title, $text])
            <li class="border-b border-line px-6 py-6 md:border-b-0 md:border-l md:first:border-l-0">
                <span class="t-badge tracking-[.14em] text-brand-600">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="t-h4 mt-3 text-brand-600">{{ $title }}</h3>
                <p class="t-body mt-2 text-ink-600">{{ $text }}</p>
            </li>
        @endforeach
    </ol>

    <p class="t-body-lg mt-6 border-l-[3px] border-crab pl-5 font-serif italic text-ink-600">
        {{ Site::text('menu.ways_note') }}
    </p>
</x-section>

{{-- AJAKAN --}}
<x-section class="relative isolate overflow-hidden pola pola-kelp bg-brand-900 text-brand-50">
    <x-gelombang :top="true" />
    <div class="grid items-end gap-10 lg:grid-cols-12 lg:gap-6">
        <div class="lg:col-span-8">
            <x-eyebrow :dark="true">{{ Site::text('menu.cta_eyebrow') }}</x-eyebrow>
            <h2 class="t-h2 max-w-[560px] [&_em]:text-brand-300">{{ Site::em('menu.cta_title') }}</h2>
            <p class="t-body-lg mt-6 max-w-[520px] text-brand-50">{{ $fill(Site::text('menu.cta_text')) }}</p>
        </div>
        <div class="flex flex-wrap gap-3 lg:col-span-4 lg:justify-end">
            <x-button href="/cabang" variant="light">{{ Site::text('menu.cta_1_label') }}</x-button>
            <x-button href="/kontak" variant="outline-light">{{ Site::text('menu.cta_2_label') }}</x-button>
        </div>
    </div>
</x-section>
@endsection