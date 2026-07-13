<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

@php
    $faviconUrl = asset('favicon-32x32.png').'?v=20260713';

    if (\Illuminate\Support\Facades\Schema::hasTable('seo_settings')) {
        $seoFavicon = \App\Models\SeoSetting::query()
            ->where('is_active', true)
            ->latest('id')
            ->value('og_image_path');

        if (is_string($seoFavicon) && trim($seoFavicon) !== '') {
            $faviconUrl = str_starts_with($seoFavicon, 'http') || str_starts_with($seoFavicon, '/')
                ? $seoFavicon
                : asset($seoFavicon);
        }
    }
@endphp

<link rel="icon" href="{{ $faviconUrl }}" type="image/png">
<link rel="shortcut icon" href="{{ $faviconUrl }}" type="image/png">
<link rel="apple-touch-icon" href="{{ $faviconUrl }}">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
