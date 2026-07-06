@props(['title' => 'Purnama Bersantai'])

@php
    $landingCssPath = public_path('landing/css/app.css');
    $landingJsPath = public_path('landing/js/app.js');
    $landingCssUrl = asset('landing/css/app.css').'?v='.(file_exists($landingCssPath) ? filemtime($landingCssPath) : time());
    $landingJsUrl = asset('landing/js/app.js').'?v='.(file_exists($landingJsPath) ? filemtime($landingJsPath) : time());

    $seoSetting = null;

    if (\Illuminate\Support\Facades\Schema::hasTable('seo_settings')) {
        $seoSetting = \App\Models\SeoSetting::query()
            ->where('is_active', true)
            ->latest('id')
            ->first();
    }

    $absoluteImageUrl = static function (?string $path): ?string {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return str_starts_with($path, '/') ? url($path) : asset($path);
    };

    $metaTitle = $seoSetting?->meta_title ?: $title;
    $siteName = $seoSetting?->site_name ?: 'Purnama Bersantai';
    $metaDescription = trim((string) ($seoSetting?->meta_description ?: 'Purnama Bersantai adalah festival musik malam dengan lineup pilihan, ticketing resmi, merchandise eksklusif, gallery moment, dan ruang kolaborasi komunitas.'));
    $metaKeywords = trim((string) ($seoSetting?->meta_keywords ?? ''));
    $canonicalUrl = $seoSetting?->canonical_url ?: url()->current();
    $robots = (($seoSetting?->robots_index ?? true) ? 'index' : 'noindex').', '.(($seoSetting?->robots_follow ?? true) ? 'follow' : 'nofollow');
    $ogTitle = $seoSetting?->og_title ?: $metaTitle;
    $ogDescription = trim((string) ($seoSetting?->og_description ?: $metaDescription));
    $ogImage = $absoluteImageUrl($seoSetting?->og_image_path) ?: asset('landing/assets/logo.png');
    $twitterTitle = $seoSetting?->twitter_title ?: $ogTitle;
    $twitterDescription = trim((string) ($seoSetting?->twitter_description ?: $ogDescription));
    $twitterImage = $absoluteImageUrl($seoSetting?->twitter_image_path) ?: $ogImage;
    $themeColor = $seoSetting?->theme_color ?: '#151515';
    $locale = $seoSetting?->locale ?: str_replace('-', '_', app()->getLocale());
    $schemaJson = is_array($seoSetting?->schema_json) && $seoSetting->schema_json !== [] ? $seoSetting->schema_json : null;
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <title>{{ $metaTitle }}</title>
        <meta name="description" content="{{ $metaDescription }}">
        @if ($metaKeywords !== '')
            <meta name="keywords" content="{{ $metaKeywords }}">
        @endif
        <meta name="robots" content="{{ $robots }}">
        <meta name="theme-color" content="{{ $themeColor }}">
        <link rel="canonical" href="{{ $canonicalUrl }}">

        @if (filled($seoSetting?->google_site_verification))
            <meta name="google-site-verification" content="{{ $seoSetting->google_site_verification }}">
        @endif
        @if (filled($seoSetting?->bing_site_verification))
            <meta name="msvalidate.01" content="{{ $seoSetting->bing_site_verification }}">
        @endif

        <meta property="og:site_name" content="{{ $siteName }}">
        <meta property="og:title" content="{{ $ogTitle }}">
        <meta property="og:description" content="{{ $ogDescription }}">
        <meta property="og:type" content="{{ $seoSetting?->og_type ?: 'website' }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:image:alt" content="{{ $ogTitle }}">
        <meta property="og:locale" content="{{ $locale }}">

        <meta name="twitter:card" content="{{ $seoSetting?->twitter_card ?: 'summary_large_image' }}">
        <meta name="twitter:title" content="{{ $twitterTitle }}">
        <meta name="twitter:description" content="{{ $twitterDescription }}">
        <meta name="twitter:image" content="{{ $twitterImage }}">

        @if ($schemaJson)
            <script type="application/ld+json">@json($schemaJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
        @endif

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <script>
            document.documentElement.classList.add('js');
        </script>
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"
        />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Squada+One&display=swap"
            rel="stylesheet"
        />
        <link rel="stylesheet" href="{{ $landingCssUrl }}" />
    </head>
    <body
        class="min-h-screen font-body text-cream antialiased"
        data-landing-asset-base="{{ asset('landing/assets') }}"
        data-landing-contact-url="{{ route('landing.contact') }}"
    >
        {{ $slot }}

        <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
        <script src="{{ $landingJsUrl }}"></script>
    </body>
</html>
