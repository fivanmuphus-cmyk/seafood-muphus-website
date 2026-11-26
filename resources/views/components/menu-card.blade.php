@props(['item', 'number', 'dark' => false, 'showCategory' => false])
<article @class(['group border-t pt-6', 'border-brand-300' => $dark, 'border-line' => ! $dark])>
    <div class="relative aspect-[4/5] overflow-hidden bg-brand-100">
        @if ($item->image_path)
            <x-photo :path="$item->image_path" :alt="$item->name" sizes="(min-width:1024px) 290px, 50vw"
                     class="h-full w-full object-cover transition-transform duration-[600ms] group-hover:scale-[1.03]" />
        @endif
        <span class="t-badge absolute left-3 top-3 bg-paper px-3 py-2 tracking-[.1em] text-ink-900 lg:left-6 lg:top-6 lg:px-3.5">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</span>
        @if ($item->badge)
            <span class="t-badge absolute right-3 top-3 bg-crab px-3 py-2 tracking-[.1em] text-white lg:right-6 lg:top-6 lg:px-4">{{ $item->badge }}</span>
        @endif
    </div>

    @if ($showCategory)
        <p class="t-badge mt-5 tracking-[.14em] text-brand-600">{{ $item->subcategoryLabel() ?: $item->categoryLabel() }}</p>
    @endif
    <h3 @class(['t-h3', 'mt-3' => $showCategory, 'mt-6' => ! $showCategory, 'text-brand-50' => $dark, 'text-ink-900' => ! $dark])>{{ $item->name }}</h3>
    @if ($item->description)
        <p @class(['t-body-sm mt-3', 'text-brand-50' => $dark, 'text-ink-600' => ! $dark])>{{ $item->description }}</p>
    @endif
    @if ($item->priceLabel())
        <p @class(['t-body-lg mt-6', 'text-brand-300' => $dark, 'text-ink-900' => ! $dark])>{{ $item->priceLabel() }}</p>
    @endif
</article>