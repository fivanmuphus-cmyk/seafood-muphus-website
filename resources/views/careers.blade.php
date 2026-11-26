@extends('layouts.app')

@section('title', 'Karir | ' . config('muphus.name'))
@section('description', 'Bergabunglah dengan ' . config('muphus.name') . '. Lihat lowongan di outlet dan kantor pusat, lalu daftar lewat formulir.')

@use('App\Support\Site')

@section('content')
@php
    $contact = config('muphus.contact');
    $hrWa = 'https://wa.me/' . $contact['hr_whatsapp'];
    $chip = 't-button border border-line bg-white px-7 py-4 text-ink-600 transition-colors hover:border-brand-600 aria-pressed:border-brand-600 aria-pressed:bg-brand-600 aria-pressed:text-white';
    $emptyMessage = fn (string $label) => str_replace(['{kategori}', '{email}'], [$label, $contact['hr_email']], Site::text('careers.empty_text'));
@endphp

<x-page-banner page="karir" title="Karir" subtitle="Bergabunglah dengan kami untuk mencapai kesuksesan bersama." image="images/banner-karir.jpg" />

{{-- LOWONGAN --}}
<x-section data-careers>
    <x-eyebrow>{{ Site::text('careers.list_eyebrow') }}</x-eyebrow>
    <h2 class="t-h2">{{ Site::em('careers.list_title', 'text-brand-600') }}</h2>
    <p class="t-body-lg mt-6 max-w-[900px] text-ink-600">{{ Site::text('careers.list_text') }}</p>

    <div class="mt-10 flex flex-wrap gap-3" role="group" aria-label="Pilih kategori karir">
        @foreach (['all' => 'Semua', 'outlet' => 'Outlet', 'office' => 'Office'] as $key => $label)
            <button type="button" data-career-filter="{{ $key }}" aria-pressed="{{ $filter === $key ? 'true' : 'false' }}" class="{{ $chip }}">{{ $label }}</button>
        @endforeach
    </div>

    {{-- Karir outlet --}}
    <div class="mt-16 {{ in_array($filter, ['all', 'outlet'], true) ? '' : 'hidden' }}" data-career-group="outlet">
        <x-eyebrow>{{ Site::text('careers.outlet_eyebrow') }}</x-eyebrow>
        <h3 class="t-h3 max-w-[760px]">{{ Site::em('careers.outlet_title', 'text-brand-600') }}</h3>
        <p class="t-body-lg mt-5 text-ink-600">{{ Site::text('careers.outlet_text') }}</p>

        @if ($outlet->isEmpty())
            <p class="mt-10 border border-dashed border-line p-8 text-center text-sm leading-6 text-ink-600">{{ $emptyMessage('outlet') }}</p>
        @else
            <div class="mt-10 grid gap-x-6 gap-y-14 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($outlet as $job)
                    <x-job-card-light :job="$job" />
                @endforeach
            </div>
        @endif
    </div>

    {{-- Karir office --}}
    <div class="mt-20 {{ in_array($filter, ['all', 'office'], true) ? '' : 'hidden' }}" data-career-group="office">
        <x-eyebrow>{{ Site::text('careers.office_eyebrow') }}</x-eyebrow>
        <h3 class="t-h3 max-w-[760px]">{{ Site::em('careers.office_title', 'text-brand-600') }}</h3>
        <p class="t-body-lg mt-5 text-ink-600">{{ Site::text('careers.office_text') }}</p>

        @if ($office->isEmpty())
            <p class="mt-10 border border-dashed border-line p-8 text-center text-sm leading-6 text-ink-600">{{ $emptyMessage('office') }}</p>
        @else
            <div class="mt-10 grid gap-x-6 gap-y-14 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($office as $job)
                    <x-job-card-light :job="$job" />
                @endforeach
            </div>
        @endif
    </div>
</x-section>

{{-- CARA MELAMAR --}}
<x-section class="relative isolate overflow-hidden pola pola-kelp bg-brand-900 text-brand-50">
    <x-gelombang :top="true" />
    <x-eyebrow :dark="true">{{ Site::text('careers.steps_eyebrow') }}</x-eyebrow>
    <h2 class="t-h2 text-white">{{ Site::em('careers.steps_title', 'text-brand-300') }}</h2>

    <ol class="mt-12 grid border-t border-white/25 md:grid-cols-3">
        @foreach (Site::rows('careers.steps', ['title', 'text']) as $i => [$title, $text])
            <li class="border-b border-white/25 py-6 md:border-b-0 md:border-l md:px-6 md:first:border-l-0 md:first:pl-0">
                <span class="t-badge tracking-[.14em] text-brand-300">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="t-h4 mt-3 text-white">{{ $title }}</h3>
                <p class="t-body mt-2 max-w-[320px] text-brand-50">{{ $text }}</p>
            </li>
        @endforeach
    </ol>

    <dl class="mt-8 grid gap-6 border-t border-white/25 pt-8 md:grid-cols-3">
        <div>
            <dt class="t-eyebrow text-brand-300">WhatsApp HRD</dt>
            <dd class="t-body-sm mt-3 text-brand-50"><a href="{{ $hrWa }}" target="_blank" rel="noopener noreferrer" class="hover:text-white">{{ $contact['hr_whatsapp_display'] }}</a></dd>
        </div>
        <div>
            <dt class="t-eyebrow text-brand-300">Email</dt>
            <dd class="t-body-sm mt-3 break-all text-brand-50"><a href="mailto:{{ $contact['hr_email'] }}" class="hover:text-white">{{ $contact['hr_email'] }}</a></dd>
        </div>
        @if (! empty($contact['hr_instagram']))
            <div>
                <dt class="t-eyebrow text-brand-300">Instagram</dt>
                <dd class="t-body-sm mt-3 text-brand-50"><a href="https://www.instagram.com/{{ $contact['hr_instagram'] }}/" target="_blank" rel="noopener noreferrer" class="hover:text-white">{{ '@' . $contact['hr_instagram'] }}</a></dd>
            </div>
        @endif
    </dl>
    <x-gelombang />
</x-section>

{{-- KENAPA MUPHUS --}}
<x-section class="pola pola-daun">
    <div class="grid gap-8 lg:grid-cols-12 lg:gap-6">
        <div class="lg:col-span-6">
            <x-eyebrow>{{ Site::text('careers.why_eyebrow') }}</x-eyebrow>
            <p class="t-body-lg text-ink-600">{{ Site::text('careers.why_company') }}</p>
            <h2 class="t-h2 mt-6">{{ Site::em('careers.why_title', 'text-brand-600') }}</h2>
        </div>
        <p class="t-body-lg max-w-[588px] text-ink-600 lg:col-span-6 lg:col-start-7 lg:pt-1">{{ Site::text('careers.why_text') }}</p>
    </div>

    <ol class="mt-12 grid border-t border-line md:grid-cols-3">
        @foreach (Site::rows('careers.benefits', ['title', 'text']) as $i => [$title, $text])
            <li class="border-b border-line py-6 md:border-l md:px-6 md:nth-[3n+1]:border-l-0 md:nth-[3n+1]:pl-0">
                <span class="t-badge tracking-[.14em] text-brand-600">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="t-h4 mt-3 text-brand-600">{{ $title }}</h3>
                <p class="t-body mt-2 text-ink-600">{{ $text }}</p>
            </li>
        @endforeach
    </ol>
    <p class="t-body mt-6 italic text-ink-600">{{ Site::text('careers.why_note') }}</p>
</x-section>

{{-- PERTANYAAN UMUM --}}
@if ($faqs->isNotEmpty())
    <x-section class="pola pola-garis">
        <div class="grid gap-10 lg:grid-cols-12 lg:gap-6">
            <div class="lg:col-span-5">
                <x-eyebrow>{{ Site::text('careers.faq_eyebrow') }}</x-eyebrow>
                <h2 class="t-h2">{{ Site::em('careers.faq_title', 'text-brand-600') }}</h2>
                <p class="t-body mt-6 max-w-[400px] text-ink-600">{{ Site::text('careers.faq_text') }}</p>
                <a href="{{ $hrWa }}" target="_blank" rel="noopener noreferrer" class="t-button mt-6 inline-block border-b border-brand-600 pb-1 text-brand-600">{{ Site::text('careers.faq_button') }}</a>
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
@endsection