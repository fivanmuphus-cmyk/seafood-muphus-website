@props(['article', 'large' => false])
<article class="group border-t border-line pt-6">
    <a href="{{ $article->url() }}" class="block">
        <div class="relative aspect-[4/3] overflow-hidden bg-brand-900">
            @if ($article->cover_path)
                <x-photo :path="$article->cover_path" :alt="$article->title" sizes="(min-width:1024px) 390px, (min-width:640px) 50vw, 72vw"
                         class="h-full w-full object-cover transition-transform duration-[600ms] group-hover:scale-[1.03]" />
            @endif
            <span class="t-badge absolute left-3 top-3 bg-paper px-3 py-2 tracking-[.14em] text-ink-900 lg:left-6 lg:top-6">{{ $article->categoryLabel() }}</span>
        </div>
        <p class="t-badge mt-6 font-normal tracking-[.14em] text-ink-600">
            {{ $article->published_at?->translatedFormat('j F Y') }} · {{ $article->readingMinutes() }} menit baca
        </p>
        <h3 @class(['mt-3 text-ink-900 transition-colors group-hover:text-brand-600', 't-h3' => $large, 't-h4' => ! $large])>{{ $article->title }}</h3>
        @if ($article->excerpt)
            <p class="t-body mt-3 text-ink-600">{{ \Illuminate\Support\Str::limit($article->excerpt, 120) }}</p>
        @endif
        <span class="t-button mt-5 inline-block border-b border-brand-600 pb-1 text-brand-600">Baca artikel</span>
    </a>
</article>