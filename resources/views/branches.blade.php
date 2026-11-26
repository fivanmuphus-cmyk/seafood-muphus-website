@extends('layouts.app')

@section('title', 'Cabang Muphus | ' . config('muphus.name'))
@section('description', 'Temukan cabang ' . config('muphus.name') . ' terdekat: lihat lokasi di peta dan buka petunjuk arahnya.')

@use('App\Support\Site')

@push('jsonld')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => $branches->map(fn ($b) => array_filter([
        '@type' => 'Restaurant',
        'name' => $b->name,
        'url' => url('/cabang'),
        'servesCuisine' => 'Seafood',
        'telephone' => $b->whatsapp ? '+' . $b->whatsapp : null,
        'address' => array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $b->address,
            'addressLocality' => $b->city,
            'addressCountry' => 'ID',
        ]),
        'geo' => ($b->latitude && $b->longitude)
            ? ['@type' => 'GeoCoordinates', 'latitude' => (float) $b->latitude, 'longitude' => (float) $b->longitude]
            : null,
        'openingHoursSpecification' => ($b->opens_at && $b->closes_at) ? [[
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'opens' => substr((string) $b->opens_at, 0, 5),
            'closes' => substr((string) $b->closes_at, 0, 5),
        ]] : null,
    ]))->values()->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endpush

@section('content')
@php
    // Peta awal diambil dari cabang pertama yang sudah punya peta, supaya cabang baru tanpa peta tidak mematikan semuanya.
    $first = $branches->first(fn ($b) => filled($b->mapEmbedUrl()));
    $chipClass = 't-button border border-line bg-white px-6 py-4 text-ink-600 transition-colors hover:border-brand-600 aria-pressed:border-brand-600 aria-pressed:bg-brand-600 aria-pressed:text-white';
    $time = fn (?string $t): string => $t ? str_replace(':', '.', substr($t, 0, 5)) : '';
@endphp

<x-page-banner page="cabang" title="Cabang Muphus" subtitle="Kunjungi Seafood Muphus di cabang terdekat!" image="images/banner-cabang.jpg" />

<x-section data-finder class="pola pola-daun">
    <x-eyebrow>{{ Site::text('branches.intro_eyebrow') }}</x-eyebrow>
    <h2 class="t-h2 max-w-[900px]">
        {{ Site::em('branches.title_1', 'text-brand-600', ['cabang' => $branches->count()]) }}<br class="hidden lg:block">
        {{ Site::text('branches.title_2') }}
    </h2>
    <p class="t-body-lg mt-6 text-ink-600">{{ Site::text('branches.intro_text') }}</p>

    @if ($branches->isEmpty())
        <p class="mt-12 border border-dashed border-line p-8 text-center text-sm text-ink-600">Daftar cabang akan segera hadir.</p>
    @else
        {{-- Pencarian dan filter kota --}}
        <div class="mt-10 flex flex-wrap items-center gap-x-5 gap-y-3">
            <label class="relative block w-full max-w-[408px] border-b border-ink-900">
                <span class="sr-only">Cari kota atau nama cabang</span>
                <svg class="absolute left-0 top-1/2 h-5 w-5 -translate-y-1/2 text-ink-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5" stroke-linecap="round"/></svg>
                <input type="search" data-search placeholder="{{ Site::text('branches.search_placeholder') }}" autocomplete="off"
                       class="t-input w-full bg-transparent py-3 pl-9 text-ink-900 placeholder:text-ink-600 focus:outline-none">
            </label>

            <div class="flex flex-wrap gap-3" role="group" aria-label="Saring berdasarkan kota">
                <button type="button" data-city-filter="all" aria-pressed="true" class="{{ $chipClass }}">Semua</button>
                @foreach ($topCities as $city)
                    <button type="button" data-city-filter="{{ $city }}" aria-pressed="false" class="{{ $chipClass }}">{{ $city }}</button>
                @endforeach
                @foreach ($otherCities as $city)
                    <button type="button" data-city-filter="{{ $city }}" data-extra-city aria-pressed="false" class="hidden {{ $chipClass }}">{{ $city }}</button>
                @endforeach
                @if ($otherCities->isNotEmpty())
                    <button type="button" data-more-cities class="t-button inline-flex items-center gap-2 border border-line bg-white px-6 py-4 text-ink-600 transition-colors hover:border-brand-600">
                        <span aria-hidden="true" data-more-icon>+</span> <span data-more-label>Lainnya</span>
                    </button>
                @endif
            </div>
        </div>

        <p class="t-body mt-12 text-ink-600" data-count aria-live="polite">Menampilkan {{ $branches->count() }} cabang</p>

        <div class="mt-6 grid gap-8 lg:grid-cols-12 lg:gap-6">
            {{-- Daftar --}}
            <div class="lg:col-span-5">
                <div class="lg:max-h-[640px] lg:overflow-y-auto lg:pr-3" data-list>
                    @foreach ($branches as $i => $b)
                        <div class="border-t border-line last:border-b" data-branch data-city="{{ $b->city }}"
                             data-search="{{ \Illuminate\Support\Str::lower($b->name . ' ' . $b->city . ' ' . $b->address) }}"
                             data-map="{{ $b->mapEmbedUrl() }}" data-name="{{ $b->name }}" data-directions="{{ $b->directionsUrl() }}" data-own-label="{{ $b->hasGoogleEmbed() ? 0 : 1 }}">
                            <button type="button" data-toggle aria-expanded="false" class="flex w-full items-center gap-5 py-6 text-left">
                                <span class="t-body-sm w-6 text-ink-600" data-number>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="t-h4 flex-1 text-ink-900">{{ $b->name }}</span>
                                <span class="text-brand-600" aria-hidden="true" data-icon>+</span>
                            </button>

                            <div class="hidden space-y-3 pb-7 pl-11" data-panel>
                                @if ($b->openStatus())
                                    <p class="flex items-center gap-2 text-xs font-medium text-brand-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>{{ $b->openStatus() }}
                                    </p>
                                @endif
                                <p class="t-body-sm text-ink-600">
                                    {{ $b->address }}
                                    @if ($b->opens_at && $b->closes_at)
                                        <br>Senin–Minggu, {{ $time($b->opens_at) }} – {{ $time($b->closes_at) }}
                                    @endif
                                    @if ($b->hours_note)
                                        <br>{{ $b->hours_note }}
                                    @endif
                                </p>
                                <div class="flex flex-wrap items-center gap-3 pt-2">
                                    @if ($b->directionsUrl())
                                        <x-button :href="$b->directionsUrl()" variant="dark" target="_blank" rel="noopener noreferrer">{{ Site::text('branches.directions_label') }}</x-button>
                                    @endif
                                    @if ($b->whatsapp)
                                        <x-button :href="'tel:+' . $b->whatsapp" variant="outline">{{ Site::text('branches.phone_label') }}</x-button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="hidden border border-dashed border-line p-8 text-center" data-empty>
                    <p class="t-body-sm text-ink-600">Belum ada cabang yang cocok. Coba kota atau kata lain, atau
                        <a href="/kontak" class="text-brand-600 underline">hubungi kami</a>.</p>
                </div>
            </div>

            {{-- Peta --}}
            <div class="order-first lg:order-none lg:col-span-6 lg:col-start-7">
                <div class="relative aspect-[16/10] overflow-hidden border border-line bg-brand-100 lg:sticky lg:top-24 lg:aspect-auto lg:h-[520px]">
                    @if ($first)
                        <iframe data-map src="{{ $first->mapEmbedUrl() }}" title="Peta lokasi cabang" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade" class="h-full w-full border-0"></iframe>
                    @endif
                    <a data-map-label href="{{ $first?->directionsUrl() }}" target="_blank" rel="noopener noreferrer"
                       @class(['absolute left-1/2 top-1/2 z-10 ml-4 flex max-w-[60%] -translate-y-[calc(50%+20px)] items-center gap-2 border border-line bg-white px-3 py-2 text-xs font-medium text-ink-900 shadow-sm transition-colors hover:border-brand-600 hover:text-brand-600', 'hidden' => ! ($first && $first->directionsUrl() && ! $first->hasGoogleEmbed())])>
                        <span class="truncate" data-map-label-text>{{ $first?->name }}</span>
                        <svg viewBox="0 0 12 12" class="h-3 w-3 shrink-0 fill-none stroke-current" stroke-width="1.5" aria-hidden="true"><path d="M3 9 9 3M4.5 3H9v4.5"/></svg>
                    </a>
                    <div data-map-empty @class(['absolute inset-0 flex items-center justify-center bg-brand-100 p-8 text-center text-sm text-ink-600', 'hidden' => (bool) $first])>
                        {{ $first ? 'Peta cabang ini belum tersedia. Gunakan tombol Petunjuk arah untuk membuka lokasinya.' : 'Peta akan tampil setelah tautan peta cabang diisi di panel admin.' }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-section>
@endsection