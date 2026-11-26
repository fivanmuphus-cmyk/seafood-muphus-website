@props(['path', 'alt' => '', 'sizes' => '100vw', 'lazy' => true])
@php $srcset = \App\Support\ImageOptimizer::srcset($path); @endphp
<img src="{{ asset('storage/' . $path) }}"
     @if ($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
     alt="{{ $alt }}"
     @if ($lazy) loading="lazy" @endif
     decoding="async"
     {{ $attributes }}>