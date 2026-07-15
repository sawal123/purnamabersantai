@props(['title' => 'Purnama Bersantai'])

@php
    $landingCssPath = public_path('landing/css/app.css');
    $landingJsPath = public_path('landing/js/app.js');
    $landingCssUrl =
        asset('landing/css/app.css') . '?v=' . (file_exists($landingCssPath) ? filemtime($landingCssPath) : time());
    $landingJsUrl =
        asset('landing/js/app.js') . '?v=' . (file_exists($landingJsPath) ? filemtime($landingJsPath) : time());
    $song = null;
    $songFile = null;

    if (\Illuminate\Support\Facades\Schema::hasTable('songs')) {
        $song = \App\Models\Song::query()->active()->ordered()->first();
    }

    if (! $song) {
        foreach (['mp3', 'wav', 'ogg', 'm4a'] as $songExtension) {
            $songMatches = glob(public_path("song/*.{$songExtension}")) ?: [];

            if ($songMatches !== []) {
                $songFile = $songMatches[0];

                break;
            }
        }
    }

    $songAssetUrl = static function (?string $path): ?string {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        return asset(collect(explode('/', $path))->map(fn (string $part) => rawurlencode($part))->implode('/'));
    };

    $songUrl = $song ? $songAssetUrl($song->audio_path) : ($songFile ? asset('song/' . rawurlencode(basename($songFile))) : null);
    $songTitle = $song ? $song->display_title : ($songFile ? pathinfo($songFile, PATHINFO_FILENAME) : 'Purnama Bersantai');

    $seoSetting = null;

    if (\Illuminate\Support\Facades\Schema::hasTable('seo_settings')) {
        $seoSetting = \App\Models\SeoSetting::query()->where('is_active', true)->latest('id')->first();
    }

    $absoluteImageUrl = static function (?string $path): ?string {
        if (!is_string($path) || trim($path) === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return str_starts_with($path, '/') ? url($path) : asset($path);
    };

    $metaTitle = $seoSetting?->meta_title ?: $title;
    $siteName = $seoSetting?->site_name ?: 'Purnama Bersantai';
    $metaDescription = trim(
        (string) ($seoSetting?->meta_description ?:
        'Purnama Bersantai adalah festival musik malam dengan lineup pilihan, ticketing resmi, merchandise eksklusif, gallery moment, dan ruang kolaborasi komunitas.'),
    );
    $metaKeywords = trim((string) ($seoSetting?->meta_keywords ?? ''));
    $canonicalUrl = $seoSetting?->canonical_url ?: url()->current();
    $robots =
        ($seoSetting?->robots_index ?? true ? 'index' : 'noindex') .
        ', ' .
        ($seoSetting?->robots_follow ?? true ? 'follow' : 'nofollow');
    $ogTitle = $seoSetting?->og_title ?: $metaTitle;
    $ogDescription = trim((string) ($seoSetting?->og_description ?: $metaDescription));
    $ogImage = $absoluteImageUrl($seoSetting?->og_image_path) ?: asset('landing/assets/logo.png');
    $twitterTitle = $seoSetting?->twitter_title ?: $ogTitle;
    $twitterDescription = trim((string) ($seoSetting?->twitter_description ?: $ogDescription));
    $twitterImage = $absoluteImageUrl($seoSetting?->twitter_image_path) ?: $ogImage;
    $faviconImage = $absoluteImageUrl($seoSetting?->og_image_path) ?: asset('favicon-32x32.png').'?v=20260713';
    $themeColor = $seoSetting?->theme_color ?: '#2f2e2e';
    $locale = $seoSetting?->locale ?: str_replace('-', '_', app()->getLocale());
    $schemaJson =
        is_array($seoSetting?->schema_json) && $seoSetting->schema_json !== [] ? $seoSetting->schema_json : null;
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

    <link rel="icon" href="{{ $faviconImage }}" type="image/png">
    <link rel="shortcut icon" href="{{ $faviconImage }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ $faviconImage }}">

    <script>
        document.documentElement.classList.add('js');
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{ $landingCssUrl }}" />
</head>

<body class="min-h-screen bg-[#ec5b00] font-body text-cream antialiased"
    data-landing-asset-base="{{ asset('landing/assets') }}" data-landing-contact-url="{{ route('landing.contact') }}">
    {{ $slot }}

    @if ($songUrl)
        @persist('landing-music-player')
            <div class="music-disc-widget" x-data="{
                open: false,
                isPlaying: false,
                hasError: false,
                togglePanel() {
                    this.open = true;
            
                    if (!this.isPlaying) {
                        this.togglePlayback();
                    }
                },
                togglePlayback() {
                    const audio = this.$refs.audio;
            
                    if (!audio) {
                        return;
                    }
            
                    this.hasError = false;
            
                    if (this.isPlaying) {
                        audio.pause();
            
                        return;
                    }
            
                    audio.play().catch(() => {
                        this.hasError = true;
                        this.isPlaying = false;
                        this.open = true;
                    });
                },
                closePanel() {
                    this.open = false;
                },
            }" x-on:keydown.escape.window="closePanel()">
                <div x-cloak x-show="open || hasError" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                    x-transition:leave="transition ease-in duration-180"
                    x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-x-4 scale-95" class="music-disc-player">
                    <div class="music-disc-player-head">
                        <div>
                            <p x-text="hasError ? 'Audio Error' : (isPlaying ? 'Now Playing' : 'Paused')"></p>
                            <h2>{{ $songTitle }}</h2>
                        </div>
                        <button type="button" aria-label="Close music panel" x-on:click="closePanel()">&times;</button>
                    </div>

                    <div class="music-disc-controls">
                        <button type="button" x-on:click="togglePlayback()"
                            x-bind:aria-label="isPlaying ? 'Pause music' : 'Play music'">
                            <svg x-show="! isPlaying" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path
                                    d="M8 5.75C8 5.1 8.72 4.72 9.25 5.09L18.1 11.34C18.57 11.67 18.57 12.33 18.1 12.66L9.25 18.91C8.72 19.28 8 18.9 8 18.25V5.75Z" />
                            </svg>
                            <svg x-show="isPlaying" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path
                                    d="M7.75 5C7.06 5 6.5 5.56 6.5 6.25V17.75C6.5 18.44 7.06 19 7.75 19H9.25C9.94 19 10.5 18.44 10.5 17.75V6.25C10.5 5.56 9.94 5 9.25 5H7.75Z" />
                                <path
                                    d="M14.75 5C14.06 5 13.5 5.56 13.5 6.25V17.75C13.5 18.44 14.06 19 14.75 19H16.25C16.94 19 17.5 18.44 17.5 17.75V6.25C17.5 5.56 16.94 5 16.25 5H14.75Z" />
                            </svg>
                        </button>
                        <span x-show="! hasError"
                            x-text="isPlaying ? 'Lagu tetap jalan saat panel ditutup.' : 'Klik play untuk lanjut.'"></span>
                        <span x-show="hasError">Audio lokal belum bisa diputar oleh browser.</span>
                    </div>
                </div>

                <audio x-ref="audio" src="{{ $songUrl }}" preload="metadata" x-on:play="isPlaying = true"
                    x-on:pause="isPlaying = false" x-on:ended="isPlaying = false"
                    x-on:error="hasError = true; isPlaying = false; open = true"></audio>

                <button type="button" class="music-disc-button" x-bind:class="{ 'is-playing': isPlaying }"
                    x-bind:aria-pressed="isPlaying.toString()" aria-label="Open music player" x-on:click="togglePanel()">
                    <span class="music-disc">
                        <span class="music-disc-ridge"></span>
                        <span class="music-disc-label">
                            <span></span>
                        </span>
                    </span>
                    <span class="music-disc-equalizer" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>
        @endpersist
    @endif

    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script src="{{ $landingJsUrl }}"></script>
</body>

</html>
