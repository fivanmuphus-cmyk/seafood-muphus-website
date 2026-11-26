@props(['top' => false])
{{-- Gelombang bertingkat di tepi bagian hijau tua: hijau tua turun bertahap ke hijau pucat lalu paper.
     Taruh di dalam <x-section> bergaya "relative isolate overflow-hidden". `top` = tepi atas. --}}
<svg viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true"
     @class([
         'pointer-events-none absolute inset-x-0 -z-[5] block h-10 w-full md:h-14 lg:h-[72px]',
         'top-0 rotate-180' => $top,
         'bottom-0' => ! $top,
     ])>
    <path class="fill-brand-600 opacity-50" d="M0 40 C260 8 520 24 780 52 C1020 78 1240 66 1440 22 L1440 121 L0 121 Z" />
    <path class="fill-brand-300 opacity-40" d="M0 58 C240 26 500 44 760 68 C1000 90 1230 82 1440 46 L1440 121 L0 121 Z" />
    <path class="fill-brand-100" d="M0 76 C250 50 500 62 760 82 C1010 100 1230 92 1440 66 L1440 121 L0 121 Z" />
    <path class="fill-none stroke-brand-200" stroke-width="1.5" vector-effect="non-scaling-stroke" d="M0 84 C260 60 500 72 760 90 C1010 106 1230 98 1440 76" />
    <path class="fill-paper" d="M0 96 C260 76 520 86 780 100 C1020 112 1240 108 1440 90 L1440 121 L0 121 Z" />
</svg>