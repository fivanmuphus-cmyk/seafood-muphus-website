@props(['eyebrow', 'dark' => false, 'href' => null, 'linkLabel' => null, 'text' => null])
<div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
    <div>
        <x-eyebrow :dark="$dark">{{ $eyebrow }}</x-eyebrow>
        <h2 @class([
            'font-serif text-[32px] leading-9 tracking-tight lg:text-[44px] lg:leading-[48px]',
            '[&_em]:text-brand-200' => $dark,
            '[&_em]:text-brand-600' => ! $dark,
        ])>{{ $slot }}</h2>
        @if ($text)
            <p @class(['mt-4 max-w-xl text-base leading-[26px]', 'text-brand-50/80' => $dark, 'text-ink-600' => ! $dark])>{{ $text }}</p>
        @endif
    </div>
    @if ($href)
        <a href="{{ $href }}" @class([
            'w-fit shrink-0 border-b pb-1 text-xs font-medium uppercase tracking-[.14em]',
            'border-brand-200 text-brand-50' => $dark,
            'border-brand-600 text-brand-600' => ! $dark,
        ])>{{ $linkLabel }}</a>
    @endif
</div>