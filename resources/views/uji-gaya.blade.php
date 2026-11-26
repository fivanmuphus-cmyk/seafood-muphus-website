@extends('layouts.app')

@section('content')
<section class="mx-auto max-w-[1200px] px-5 py-[72px] md:px-8 lg:px-10 lg:py-[120px]">
    <p class="mb-4 flex items-center gap-3 text-xs font-medium uppercase tracking-[.18em] text-brand-600">
        <span class="h-px w-7 bg-brand-600"></span> Pengujian gaya
    </p>
    <h1 class="font-serif text-[44px] leading-[48px] tracking-tight lg:text-[72px] lg:leading-[76px]">
        Seafood segar, <em class="text-brand-600">hangat</em> di meja keluarga.
    </h1>
    <p class="mt-6 max-w-xl text-lg leading-[30px] text-ink-600">Paragraf contoh dengan Inter 18/30.</p>
    <div class="mt-8 flex flex-wrap gap-3">
        <a href="#" class="bg-brand-600 px-6 py-4 text-xs font-medium uppercase tracking-[.14em] text-white hover:bg-brand-500">Lihat menu</a>
        <a href="#" class="border border-brand-600 px-6 py-4 text-xs font-medium uppercase tracking-[.14em] text-brand-600">Temukan cabang</a>
    </div>
</section>
@endsection