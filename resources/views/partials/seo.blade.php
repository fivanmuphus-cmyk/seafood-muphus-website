@php
    // Nilai @section('title') dan lainnya sudah ter-escape oleh Blade; dibuka dulu agar tidak ter-escape dua kali.
    $seo = fn (string $name, string $fallback = '') => html_entity_decode(trim($__env->yieldContent($name)), ENT_QUOTES) ?: $fallback;

    $siteName = config('muphus.name');
    $title = $seo('title', $siteName);
    $description = $seo('description', $siteName . ', seafood tenda kaki lima modern untuk keluarga.');
    $canonical = $seo('canonical', url()->current()); // tanpa ?query, jadi hasil pencarian tidak jadi halaman ganda
    $image = $seo('og_image', asset('images/og-muphus.jpg'));
    $type = $seo('og_type', 'website');
    $robots = $seo('robots', 'index,follow');
    $social = array_column(\App\Support\Site::socialLinks(), 'url');

    $organization = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Restaurant',
        'name' => $siteName,
        'url' => url('/'),
        'logo' => asset('images/logo.png'),
        'image' => asset('images/og-muphus.jpg'),
        'description' => config('muphus.tagline'),
        'servesCuisine' => 'Seafood',
        'telephone' => config('muphus.contact.whatsapp') ? '+' . config('muphus.contact.whatsapp') : null,
        'email' => config('muphus.contact.email'),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => config('muphus.contact.address'),
            'addressCountry' => 'ID',
        ],
        'sameAs' => $social ?: null,
    ]);
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $canonical }}">
<meta name="robots" content="{{ $robots }}">

<meta property="og:locale" content="id_ID">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:image:alt" content="{{ $title }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">

<script type="application/ld+json">{!! json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@stack('jsonld')
