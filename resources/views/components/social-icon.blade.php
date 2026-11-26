@props(['name' => null])
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" {{ $attributes }}>
    @switch($name)
        @case('instagram')
            <rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r=".6" fill="currentColor"/>
            @break
        @case('facebook')
            <path d="M14.5 21v-8h2.7l.5-3.2h-3.2V7.9c0-.9.4-1.6 1.7-1.6h1.6V3.5S16.4 3.2 15.2 3.2c-2.6 0-4.2 1.6-4.2 4.3v2.3H8.3V13H11v8"/>
            @break
        @case('tiktok')
            <path d="M14 3v11.5a3.7 3.7 0 1 1-3.7-3.7"/><path d="M14 3c.3 2.7 2 4.5 5 4.8"/>
            @break
        @case('threads')
            <path d="M17.5 11.2C17 7.8 15 6 12.1 6 8.8 6 6.5 8.4 6.5 12s2.3 6 5.7 6c2.7 0 4.4-1.3 4.4-3.1 0-2-1.8-3.1-4.3-3.1-1.7 0-2.9.7-2.9 1.9 0 1.1.9 1.8 2.2 1.8 1.9 0 3.1-1.3 3.1-4.3"/>
            @break
        @case('youtube')
            <rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="M10 9.5v5l4.3-2.5z"/>
            @break
        @default
            {{-- Akun tambahan dari admin: ikon tautan umum --}}
            <circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3z"/>
    @endswitch
</svg>
