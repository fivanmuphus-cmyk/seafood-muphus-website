@extends('layouts.app')

@section('title', 'Kontak | ' . config('muphus.name'))
@section('description', 'Hubungi ' . config('muphus.name') . ' untuk informasi, kemitraan, karir, atau saran. Chat WhatsApp atau kirim pesan lewat formulir.')

@use('App\Support\Site')

@section('content')
@php
    $c = config('muphus.contact');
    $social = \App\Support\Site::socialLinks();
    $hours = Site::text('contact.hours');
    $hours = $hours === '-' ? '' : $hours;
    $wa = 'https://wa.me/' . $c['whatsapp'];
    $partnerNumber = $c['partnership_whatsapp'] ?? null;
    $field = 't-input w-full appearance-none rounded-none border-0 border-b bg-transparent px-0 py-3 text-ink-900 placeholder:text-ink-600 focus:border-brand-600 focus:outline-none focus:ring-0';
    $border = fn (string $name) => $errors->has($name) ? 'border-crab' : 'border-ink-900';
    $rows = array_filter([
        ['Alamat', $c['address'], null],
        ['WhatsApp', $c['whatsapp_display'], 'Informasi umum'],
        $partnerNumber ? ['WhatsApp Kemitraan', $c['partnership_whatsapp_display'] ?? $partnerNumber, 'Khusus kemitraan'] : null,
        ['WhatsApp HRD', $c['hr_whatsapp_display'], 'Khusus karir dan lowongan'],
        ['Telepon', $c['whatsapp_display'], null],
        ['Email', $c['email'], null],
        $hours ? ['Jam layanan', $hours, null] : null,
    ]);
@endphp

<x-page-banner page="kontak" title="Kontak" subtitle="Hubungi kami untuk informasi lebih lanjut." image="images/banner-kontak.jpg" />

{{-- INFO DAN FORMULIR --}}
<x-section class="pola pola-daun">
    <x-eyebrow>{{ Site::text('contact.intro_eyebrow') }}</x-eyebrow>

    <div class="grid gap-14 lg:grid-cols-12 lg:gap-6">
        {{-- Info kontak --}}
        <div class="lg:col-span-5">
            <h2 class="t-h2">{{ Site::em('contact.intro_title', 'text-brand-600') }}</h2>
            <p class="t-body-lg mt-6 text-ink-600">{{ Site::text('contact.intro_text') }}</p>

            <dl class="mt-10 border-b border-line">
                @foreach ($rows as [$label, $value, $note])
                    <div class="grid grid-cols-[130px_1fr] gap-4 border-t border-line py-6">
                        <dt class="t-eyebrow pt-0.5 text-brand-600">{{ $label }}</dt>
                        <dd class="t-body text-ink-600">
                            @if ($label === 'Email')
                                <a href="mailto:{{ $value }}" class="break-words hover:text-brand-600">{{ $value }}</a>
                            @elseif (in_array($label, ['WhatsApp', 'WhatsApp Kemitraan', 'WhatsApp HRD', 'Telepon'], true))
                                <a href="{{ match ($label) { 'WhatsApp HRD' => 'https://wa.me/' . $c['hr_whatsapp'], 'WhatsApp Kemitraan' => 'https://wa.me/' . $partnerNumber, 'Telepon' => 'tel:+' . $c['whatsapp'], default => $wa } }}"
                                   @if ($label !== 'Telepon') target="_blank" rel="noopener noreferrer" @endif class="hover:text-brand-600">{{ $value }}</a>
                            @else
                                {{ $value }}
                            @endif
                            @if ($note)
                                <span class="mt-2 block">{{ $note }}</span>
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>

            @if ($social)
                <div class="mt-8 flex flex-wrap gap-3">
                    @foreach ($social as $link)
                        <x-button :href="$link['url']" variant="outline" target="_blank" rel="noopener noreferrer" class="!border-ink-600">{{ $link['label'] }}</x-button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Formulir --}}
        <div class="scroll-mt-28 lg:col-span-6 lg:col-start-7" id="kirim-pesan">
            <h2 class="t-h2">{{ Site::text('contact.form_title') }}</h2>

            <div class="mt-8 flex flex-col gap-4 border border-line bg-paper p-6 sm:flex-row sm:items-center sm:justify-between">
                <p class="t-body-sm text-ink-600">{{ Site::text('contact.chat_note') }}</p>
                <x-button :href="$wa" target="_blank" rel="noopener noreferrer" class="w-fit shrink-0">{{ Site::text('contact.chat_button') }}</x-button>
            </div>

            @if (session('sent'))
                <div class="mt-8 border border-brand-600 bg-paper p-6" role="status">
                    <p class="t-h4 text-brand-600">{{ Site::text('contact.success_title') }}</p>
                    <p class="t-body mt-2 text-ink-600">{{ Site::text('contact.success_text') }}</p>
                </div>
            @else
                <form method="post" action="/kontak" class="mt-8" novalidate>
                    @csrf

                    @if ($errors->any())
                        <p class="t-body-sm mb-6 border border-crab p-4 text-crab" role="alert">Mohon periksa kembali isian yang ditandai.</p>
                    @endif

                    {{-- Kolom jebakan untuk robot (tidak terlihat oleh manusia) --}}
                    <div class="absolute -left-[9999px]" aria-hidden="true">
                        <label>Jangan diisi <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <div class="grid gap-x-8 gap-y-7 sm:grid-cols-2">
                        <div>
                            <label for="name" class="t-label text-ink-600">Nama lengkap</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" autocomplete="name" maxlength="100"
                                   class="{{ $field }} {{ $border('name') }}">
                            @error('name') <p class="t-body-sm mt-2 text-crab">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="t-label text-ink-600">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email" maxlength="150"
                                   class="{{ $field }} {{ $border('email') }}">
                            @error('email') <p class="t-body-sm mt-2 text-crab">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="phone" class="t-label text-ink-600">Nomor WhatsApp</label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="08xx xxxx xxxx" autocomplete="tel" maxlength="20"
                                   class="{{ $field }} {{ $border('phone') }}">
                            @error('phone') <p class="t-body-sm mt-2 text-crab">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="topic" class="t-label text-ink-600">Topik</label>
                            <div class="relative">
                                <select id="topic" name="topic" class="{{ $field }} {{ $border('topic') }} pr-8">
                                    <option value="">Pilih topik</option>
                                    @foreach ($topics as $key => $label)
                                        <option value="{{ $key }}" @selected(old('topic') === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <svg class="pointer-events-none absolute right-0 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m5 9 7 7 7-7"/></svg>
                            </div>
                            @error('topic') <p class="t-body-sm mt-2 text-crab">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="message" class="t-label text-ink-600">Pesan</label>
                            <textarea id="message" name="message" rows="6" maxlength="2000" placeholder="Tulis pesan Anda di sini"
                                      class="{{ $field }} {{ $border('message') }} resize-y">{{ old('message') }}</textarea>
                            @error('message') <p class="t-body-sm mt-2 text-crab">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <button type="submit" class="t-button mt-8 bg-brand-600 px-6 py-4 text-white transition-colors hover:bg-brand-500">Kirim pesan</button>
                </form>
            @endif
        </div>
    </div>
</x-section>

{{-- PERTANYAAN UMUM --}}
@if ($faqs->isNotEmpty())
    <x-section class="pola pola-garis">
        <div class="grid gap-10 lg:grid-cols-12 lg:gap-6">
            <div class="lg:col-span-5">
                <x-eyebrow>{{ Site::text('contact.faq_eyebrow') }}</x-eyebrow>
                <h2 class="t-h2">{{ Site::em('contact.faq_title', 'text-brand-600') }}</h2>
                <p class="t-body mt-6 max-w-[400px] text-ink-600">{{ Site::text('contact.faq_text') }}</p>
                <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer" class="t-button mt-6 inline-block border-b border-brand-600 pb-1 text-brand-600">{{ Site::text('contact.faq_button') }}</a>
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