@props(['job'])
<article class="flex h-full flex-col border-t border-brand-50/60 pt-5">
    <div class="relative aspect-[3/4] overflow-hidden border border-brand-50/60">
        @if ($job->brochure_path)
            <button type="button" class="block h-full w-full"
                    data-lightbox="{{ asset('storage/' . $job->brochure_path) }}"
                    data-alt="Brosur lowongan {{ $job->position }}"
                    aria-label="Perbesar brosur {{ $job->position }}">
                <x-photo :path="$job->brochure_path" :alt="'Brosur lowongan ' . $job->position" sizes="(min-width:1024px) 390px, 72vw" class="h-full w-full object-contain" />
            </button>
        @endif
        <span class="t-badge absolute right-3 top-3 bg-crab px-3 py-2 tracking-[.14em] text-white lg:right-5 lg:top-5">
            @if ($job->deadline)
                Sampai {{ $job->deadline->translatedFormat('j M') }}
            @else
                Segera dibutuhkan
            @endif
        </span>
    </div>

    @if ($job->locationLabel())
        <p class="t-h4 mt-6 text-brand-300">{{ $job->locationLabel() }}</p>
    @endif
    <h3 class="t-h3 mt-2 text-brand-50">{{ $job->position }}</h3>
    @if ($job->hook)
        <p class="t-body-sm mt-3 flex-1 text-brand-50">{{ $job->hook }}</p>
    @else
        <div class="flex-1"></div>
    @endif

    @if ($job->safeFormUrl())
        <x-button :href="$job->safeFormUrl()" variant="light" target="_blank" rel="noopener noreferrer" class="mt-6 w-fit">Daftar Sekarang</x-button>
    @endif
</article>