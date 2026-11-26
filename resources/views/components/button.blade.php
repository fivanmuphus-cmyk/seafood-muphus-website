@props(['href' => '#', 'variant' => 'solid'])
@php
    $styles = [
        'solid' => 'bg-brand-600 text-white hover:bg-brand-500',
        'dark' => 'bg-brand-900 text-white hover:bg-brand-800',
        'light' => 'bg-brand-50 text-brand-900 hover:bg-white',
        'outline' => 'border border-brand-600 text-brand-600 hover:bg-brand-600 hover:text-white',
        'outline-light' => 'border border-brand-50 text-brand-50 hover:bg-brand-50 hover:text-brand-900',
        'ghost' => 'border border-white/50 text-white hover:bg-white/10',
    ];
@endphp
<a href="{{ $href }}" {{ $attributes->class(['t-button inline-flex items-center justify-center px-6 py-4 transition-colors', $styles[$variant] ?? $styles['solid']]) }}>{{ $slot }}</a>