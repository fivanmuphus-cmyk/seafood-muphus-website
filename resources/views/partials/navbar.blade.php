@php
    $logo = collect(['images/logo.svg', 'images/logo.webp'])->first(fn ($file) => file_exists(public_path($file)));
@endphp
<header class="sticky top-0 z-40 border-b border-line bg-paper/90 backdrop-blur">
    <div class="mx-auto grid h-16 max-w-[1200px] grid-cols-[1fr_auto] items-center px-5 md:h-[80px] md:px-8 lg:px-10 xl:grid-cols-[1fr_auto_1fr]">

        {{-- Logo --}}
        <a href="/" class="justify-self-start focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-600"
           aria-label="{{ config('muphus.name') }}, ke beranda">
            @if ($logo)
                <img src="{{ asset($logo) }}" alt="{{ config('muphus.name') }}" class="h-9 w-auto md:h-10">
            @else
                <span class="t-h4 text-brand-600">{{ config('muphus.name') }}</span>
            @endif
        </a>

        {{-- Menu tengah --}}
        <nav class="t-nav hidden items-center gap-5 xl:flex" aria-label="Menu utama">
            @foreach (config('muphus.nav') as [$label, $path])
                @php $active = $path === '/' ? request()->is('/') : request()->is(ltrim($path, '/') . '*'); @endphp
                <a href="{{ $path }}"
                   @if ($active) aria-current="page" @endif
                   @class([
                       'relative py-1 transition-colors focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-600',
                       'text-brand-600' => $active,
                       'text-ink-600 hover:text-brand-600 after:absolute after:bottom-0 after:left-0 after:h-px after:w-full after:origin-left after:scale-x-0 after:bg-brand-600 after:transition-transform after:duration-300 hover:after:scale-x-100 focus-visible:after:scale-x-100' => ! $active,
                   ])>{{ $label }}</a>
            @endforeach
        </nav>

        {{-- Kanan: Hubungi kami + hamburger --}}
        <div class="flex items-center gap-6 justify-self-end">
            <a href="/kontak"
               class="t-button hidden border-b border-brand-600 pb-1 text-brand-600 transition-colors hover:border-brand-500 hover:text-brand-500 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-600 xl:inline-block">
                Hubungi kami
            </a>

            <button type="button" id="menu-toggle" class="p-1 xl:hidden" aria-label="Buka menu" aria-expanded="false" aria-controls="mobile-menu">
                <span class="block h-0.5 w-6 bg-ink-900"></span>
                <span class="mt-1.5 block h-0.5 w-6 bg-ink-900"></span>
                <span class="mt-1.5 block h-0.5 w-6 bg-ink-900"></span>
            </button>
        </div>
    </div>

    {{-- Menu seluler --}}
    <div id="mobile-menu" class="hidden bg-brand-900 px-5 pb-8 pt-4 xl:hidden">
        @foreach (array_merge(config('muphus.nav'), [['Kontak', '/kontak']]) as [$label, $path])
            <a href="{{ $path }}" class="t-h3 block border-b border-white/15 py-3 text-brand-50">{{ $label }}</a>
        @endforeach
        <a href="https://wa.me/{{ config('muphus.contact.whatsapp') }}" target="_blank" rel="noopener noreferrer"
           class="t-button mt-6 flex items-center justify-center bg-brand-600 px-6 py-4 text-white">Chat WhatsApp</a>
    </div>
</header>