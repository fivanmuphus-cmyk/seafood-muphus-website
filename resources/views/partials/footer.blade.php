@php
    // Garis bawah yang meluncur saat disorot, sama seperti menu di navbar. Warna garis mengikuti warna tautan.
    $garisDasar = 'relative py-0.5 transition-colors hover:text-brand-200 after:absolute after:bottom-0 after:left-0 after:h-px after:w-full after:origin-left after:scale-x-0 after:bg-current after:transition-transform after:duration-300 hover:after:scale-x-100 focus-visible:after:scale-x-100 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-300';
    $garis = 'inline-block ' . $garisDasar;
@endphp
<footer class="bg-brand-900 text-brand-50">
    <div class="h-0.5 bg-gradient-to-r from-brand-600 to-crab"></div>
    <div class="mx-auto max-w-[1200px] px-5 py-16 md:px-8 lg:px-10 lg:py-24">
        <div class="grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-3">
                @if (file_exists(public_path('images/logo-putih.webp')))
                    <img src="{{ asset('images/logo-putih.webp') }}" alt="{{ config('muphus.name') }}" class="h-10 w-auto">
                    <p class="mt-4 max-w-xs text-sm leading-6 text-brand-50/80">{{ config('muphus.name') }}. {{ config('muphus.tagline') }}</p>
                @else
                    <p class="font-serif text-3xl">{{ config('muphus.name') }}</p>
                    <p class="mt-3 max-w-xs text-sm leading-6 text-brand-50/80">{{ config('muphus.tagline') }}</p>
                @endif
            </div>

            <div class="lg:col-span-2">
                <p class="mb-5 text-[11px] font-medium uppercase tracking-[.18em] text-brand-300">Jelajahi</p>
                <ul class="space-y-2 text-sm">
                    @foreach (array_merge(config('muphus.nav'), [['Kontak', '/kontak']]) as [$label, $path])
                        <li><a href="{{ $path }}" class="{{ $garis }}">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-4">
                <p class="mb-5 text-[11px] font-medium uppercase tracking-[.18em] text-brand-300">Hubungi kami</p>
                <ul class="space-y-3 text-sm leading-6">
                    <li>{{ config('muphus.contact.address') }}</li>
                    <li>
                        <a href="https://wa.me/{{ config('muphus.contact.whatsapp') }}" target="_blank" rel="noopener noreferrer" class="{{ $garis }}">
                            WhatsApp {{ config('muphus.contact.whatsapp_display') }}
                        </a>
                    </li>
                    <li><a href="mailto:{{ config('muphus.contact.email') }}" class="{{ $garis }} [overflow-wrap:anywhere] lg:whitespace-nowrap">{{ config('muphus.contact.email') }}</a></li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                <p class="mb-5 text-[11px] font-medium uppercase tracking-[.18em] text-brand-300">Cabang</p>
                <ul class="space-y-2 text-sm">
                    @foreach ($footerBranches ?? [] as $branch)
                        <li><a href="/cabang" class="{{ $garis }}">{{ $branch->name }}</a></li>
                    @endforeach
                    <li class="pt-2">
                        <a href="/cabang" class="{{ $garis }} font-medium text-sand hover:text-white">Lihat semua cabang <span aria-hidden="true">&rarr;</span></a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-16 flex flex-col gap-4 border-t border-brand-50/15 pt-8 text-xs text-brand-50/70 md:flex-row md:items-center md:justify-between">
            <p class="flex flex-wrap gap-x-4 gap-y-2">
                <span>© {{ date('Y') }} {{ config('muphus.name') }}. Hak cipta dilindungi.</span>
                <a href="/kebijakan-privasi" class="{{ $garis }}">Kebijakan Privasi</a>
                @if (preg_match('/^G-[A-Z0-9]{4,15}$/', (string) config('muphus.analytics.id')))
                    <button type="button" data-consent-reset class="{{ $garis }}">Pengaturan cookie</button>
                @endif
            </p>
            <p class="flex flex-wrap items-center gap-x-6 gap-y-3">
                @foreach (\App\Support\Site::socialLinks() as $link)
                    <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 {{ $garisDasar }} text-brand-50/80 hover:text-white">
                        <x-social-icon :name="$link['key']" class="h-4 w-4 shrink-0" />
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </p>
        </div>
    </div>
</footer>