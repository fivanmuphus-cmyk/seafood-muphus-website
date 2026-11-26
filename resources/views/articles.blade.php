@extends('layouts.app')

@section('title', 'Artikel | ' . config('muphus.name'))
@section('description', 'Cerita dan kabar seputar ' . config('muphus.name') . ': panduan memilih bahan, ide masakan, dan kabar terbaru.')

@use('App\Support\Site')

@if (($q ?? '') !== '')
    @section('robots', 'noindex,follow')
@endif

@section('content')
@php
    $social = array_filter(config('muphus.social'));
@endphp

<x-page-banner page="artikel" title="Artikel" subtitle="Seafood Muphus, Seafood Tenda Terbesar Andalan Keluarga" image="images/banner-artikel.jpg" />

<x-section data-menu class="pola pola-garis">
    <x-eyebrow>{{ Site::text('articles.intro_eyebrow') }}</x-eyebrow>
    <h2 class="t-h2">{{ Site::em('articles.intro_title', 'text-brand-600') }}</h2>

    <form method="get" action="/artikel" role="search" class="mt-10">
        <label class="relative block w-full max-w-[408px] border-b border-ink-900">
            <span class="sr-only">Cari dengan kata kunci artikel</span>
            <svg class="absolute left-0 top-1/2 h-5 w-5 -translate-y-1/2 text-ink-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5" stroke-linecap="round"/></svg>
            <input type="search" name="q" value="{{ $q }}" placeholder="{{ Site::text('articles.search_placeholder') }}" autocomplete="off"
                   class="t-input w-full bg-transparent py-3 pl-9 text-ink-900 placeholder:text-ink-600 focus:outline-none">
        </label>
    </form>

    @if ($q !== '')
        <p class="t-body mt-8 text-ink-600">
            {{ $articles->count() }} artikel untuk “{{ $q }}”.
            <a href="/artikel" class="text-brand-600 underline">Tampilkan semua</a>
        </p>
    @endif

    @if ($articles->isEmpty())
        <p class="mt-12 border border-dashed border-line p-8 text-center text-sm text-ink-600">
            {{ $q !== '' ? 'Tidak ada artikel untuk kata itu. Coba kata kunci lain.' : 'Artikel akan segera hadir.' }}
        </p>
    @else
        {{-- Artikel unggulan --}}
        @if ($featured)
            <article class="group mt-12 grid gap-8 lg:grid-cols-12 lg:gap-6">
                <a href="{{ $featured->url() }}" class="block overflow-hidden border border-line bg-paper lg:col-span-7">
                    <div class="aspect-[4/3] lg:aspect-[13/10]">
                        @if ($featured->cover_path)
                            <img src="{{ asset('storage/' . $featured->cover_path) }}" alt="{{ $featured->title }}"
                                 class="h-full w-full object-cover transition-transform duration-[600ms] group-hover:scale-[1.03]">
                        @endif
                    </div>
                </a>
                <div class="flex flex-col justify-between gap-8 lg:col-span-5 lg:col-start-8">
                    <div>
                        <p class="t-badge font-normal tracking-[.14em] text-ink-600">
                            {{ $featured->published_at?->translatedFormat('j F Y') }} · {{ $featured->readingMinutes() }} menit baca
                        </p>
                        <h3 class="t-h2 mt-4 text-ink-900">
                            <a href="{{ $featured->url() }}" class="hover:text-brand-600">{{ $featured->title }}</a>
                        </h3>
                        @if ($featured->excerpt)
                            <p class="t-body-lg mt-4 text-ink-600">{{ $featured->excerpt }}</p>
                        @endif
                    </div>
                    <a href="{{ $featured->url() }}" class="t-button w-fit border-b border-brand-600 pb-1 text-brand-600">{{ Site::text('articles.read_label') }}</a>
                </div>
            </article>
        @endif

        {{-- Daftar artikel --}}
        @if ($rest->isNotEmpty())
            <div class="mt-14 grid gap-x-6 gap-y-14 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($rest as $article)
                    <div data-card><x-article-card :article="$article" /></div>
                @endforeach
            </div>

            <div class="mt-14 text-center">
                <button type="button" data-more class="t-button border-b border-brand-600 pb-1 text-brand-600">{{ Site::text('articles.more_label') }}</button>
            </div>
        @endif
    @endif
</x-section>

{{-- IKUTI KAMI --}}
@if ($social)
    <x-section class="pola pola-daun">
        <div class="grid items-center gap-10 lg:grid-cols-12 lg:gap-6">
            <div class="lg:col-span-8">
                <h2 class="t-h2">{{ Site::em('articles.follow_title', 'text-brand-600') }}</h2>
                <p class="t-body-lg mt-6 text-ink-600">{{ Site::text('articles.follow_text') }}</p>
            </div>
            <div class="flex flex-wrap gap-3 lg:col-span-4 lg:justify-end">
                @isset($social['instagram'])
                    <x-button :href="$social['instagram']" variant="outline" target="_blank" rel="noopener noreferrer">Instagram</x-button>
                @endisset
                @isset($social['tiktok'])
                    <x-button :href="$social['tiktok']" target="_blank" rel="noopener noreferrer">TikTok</x-button>
                @endisset
            </div>
        </div>
    </x-section>
@endif
@endsection