@props(['job'])
<article class="flex h-full flex-col border-t border-line pt-6">
    <div class="relative aspect-[3/4] overflow-hidden bg-brand-900">
        @if ($job->brochure_path)
            <button type="button" class="block h-full w-full"
                    data-lightbox="{{ asset('storage/' . $job->brochure_path) }}"
                    data-alt="Brosur lowongan {{ $job->position }}"
                    aria-label="Perbesar brosur {{ $job->position }}">
                <x-photo :path="$job->brochure_path" :alt="'Brosur lowongan ' . $job->position" sizes="(min-width:1024px) 390px, (min-width:640px) 50vw, 100vw" class="h-full w-full object-contain" />
            </button>
        @endif
        <span class="t-badge absolute right-3 top-3 bg-crab px-3 py-2 tracking-[.14em] text-white lg:right-6 lg:top-6">
            @if ($job->deadline)
                Sampai {{ $job->deadline->translatedFormat('j M') }}
            @else
                Segera dibutuhkan
            @endif
        </span>
    </div>

    @if ($job->locationLabel())
        <p class="t-badge mt-6 font-normal tracking-[.14em] text-ink-600">{{ $job->locationLabel() }}</p>
    @endif
    <h3 class="t-h4 mt-3 text-ink-900">{{ $job->position }}</h3>
    @if ($job->summary ?: $job->hook)
        <p class="t-body mt-3 flex-1 text-ink-600">{{ $job->summary ?: $job->hook }}</p>
    @else
        <div class="flex-1"></div>
    @endif

    @if ($job->safeFormUrl())
        <x-button :href="$job->safeFormUrl()" target="_blank" rel="noopener noreferrer" class="mt-6 w-fit">Daftar sekarang</x-button>
    @endif
</article>