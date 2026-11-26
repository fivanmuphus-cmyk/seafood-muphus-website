@extends('layouts.app')

@section('title', ($article->seo_title ?: $article->title) . ' | ' . config('muphus.name'))
@section('description', $article->seo_description ?: \Illuminate\Support\Str::limit((string) $article->excerpt, 155))
@section('og_type', 'article')
@if ($article->cover_path)
    @section('og_image', \Illuminate\Support\Facades\Storage::disk('public')->url($article->cover_path))
@endif

@push('jsonld')
<script type="application/ld+json">{!! json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $article->title,
    'description' => $article->seo_description ?: \Illuminate\Support\Str::limit((string) $article->excerpt, 155),
    'image' => $article->cover_path ? [\Illuminate\Support\Facades\Storage::disk('public')->url($article->cover_path)] : null,
    'datePublished' => optional($article->published_at)->toIso8601String(),
    'dateModified' => optional($article->updated_at)->toIso8601String(),
    'mainEntityOfPage' => url($article->url()),
    'author' => ['@type' => 'Organization', 'name' => config('muphus.name')],
    'publisher' => ['@type' => 'Organization', 'name' => config('muphus.name'), 'logo' => ['@type' => 'ImageObject', 'url' => asset('images/logo.png')]],
]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endpush

@section('content')
@php
    $toc = $article->tableOfContents();
    $shareUrl = url($article->url());
    $waUrl = 'https://wa.me/?text=' . rawurlencode($article->title . ' ' . $shareUrl);
    $shareIcon = '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="18" cy="5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="19" r="2.5"/><path d="m8.2 10.8 7.6-4.4M8.2 13.2l7.6 4.4"/></svg>';
@endphp

<div class="mx-auto max-w-[1200px] px-5 pt-10 md:px-8 lg:px-10 lg:pt-14">
    {{-- Tautan kembali dan breadcrumb --}}
    <div class="flex items-center justify-between gap-6">
        <a href="/artikel" class="t-button inline-flex items-center gap-3 border-b border-brand-600 pb-1 text-brand-600">
            <span aria-hidden="true">←</span> Kembali ke daftar artikel
        </a>
        <nav class="t-caption hidden text-ink-600 md:block" aria-label="Breadcrumb">
            <a href="/" class="hover:text-brand-600">Beranda</a> / <a href="/artikel" class="hover:text-brand-600">Artikel</a> / {{ \Illuminate\Support\Str::limit($article->title, 40) }}
        </nav>
    </div>

    {{-- Judul --}}
    <header class="mx-auto mt-10 text-center lg:mt-12">
        <h1 class="t-h1 mx-auto max-w-[900px] text-ink-900">{{ $article->title }}</h1>
        @if ($article->excerpt)
            <p class="t-body-lg mx-auto mt-6 max-w-[700px] text-ink-600">{{ $article->excerpt }}</p>
        @endif
        <p class="t-badge mt-6 font-normal tracking-[.14em] text-ink-600">
            {{ $article->published_at?->translatedFormat('j F Y') }} · {{ $article->readingMinutes() }} menit baca
        </p>
    </header>

    {{-- Foto utama --}}
    <figure class="mt-12">
        <div class="aspect-[4/3] overflow-hidden bg-brand-100 lg:aspect-[21/9]">
            @if ($article->cover_path)
                <img src="{{ asset('storage/' . $article->cover_path) }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
            @endif
        </div>
    </figure>

    {{-- Baris kategori dan bagikan --}}
    <div class="mt-4 flex items-center justify-between gap-4 border-y border-line py-4">
        <p class="t-body-sm text-ink-600">Artikel · {{ $article->categoryLabel() }}</p>
        <div class="flex items-center gap-5 text-brand-600">
            {!! $shareIcon !!}
            <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="t-button border-b border-brand-600 pb-1">WhatsApp</a>
            <button type="button" data-copy-link="{{ $shareUrl }}" class="t-button border-b border-brand-600 pb-1">Salin tautan</button>
        </div>
    </div>

    {{-- Isi --}}
    <div class="mt-12 grid gap-12 lg:grid-cols-12 lg:gap-6">
        @if (count($toc) > 1)
            <aside class="hidden lg:col-span-3 lg:block">
                <div class="lg:sticky lg:top-28">
                    <p class="t-eyebrow border-b border-line pb-4 text-brand-600">Dalam artikel ini</p>
                    <ul class="mt-4 space-y-4">
                        @foreach ($toc as $item)
                            <li><a href="#{{ $item['id'] }}" class="t-body-sm text-ink-600 hover:text-brand-600">{{ $item['text'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        @endif

        <div @class(['lg:col-span-8', 'lg:col-start-4' => count($toc) > 1, 'lg:col-start-3' => count($toc) <= 1])>
            {{-- Hasil Markdown yang sudah dibersihkan (HTML mentah dibuang), aman ditampilkan. --}}
            <div class="article-body">{!! $article->bodyHtml() !!}</div>

            <div class="mt-14 flex flex-wrap items-center justify-between gap-4 border-t border-line pt-6">
                <a href="/artikel" class="t-button inline-flex items-center gap-3 border-b border-brand-600 pb-1 text-brand-600">
                    <span aria-hidden="true">←</span> Kembali ke daftar artikel
                </a>
                <div class="flex items-center gap-5 text-brand-600">
                    {!! $shareIcon !!}
                    <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="t-button border-b border-brand-600 pb-1">WhatsApp</a>
                    <button type="button" data-copy-link="{{ $shareUrl }}" class="t-button border-b border-brand-600 pb-1">Salin tautan</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ARTIKEL TERKAIT --}}
@if ($related->isNotEmpty())
    <x-section class="pola pola-daun mt-[72px] lg:mt-[120px]">
        <div class="mb-12 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <x-eyebrow>Artikel terkait</x-eyebrow>
                <h2 class="t-h2">Cerita lain untuk <span class="text-brand-600">dinikmati.</span></h2>
            </div>
            <a href="/artikel" class="t-button w-fit shrink-0 border-b border-brand-600 pb-1 text-brand-600">Lihat semua artikel</a>
        </div>
        <div class="grid gap-x-6 gap-y-14 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($related as $item)
                <x-article-card :article="$item" :large="true" />
            @endforeach
        </div>
    </x-section>
@endif
@endsection