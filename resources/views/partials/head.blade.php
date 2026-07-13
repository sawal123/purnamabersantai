<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="{{ asset('favicon.svg') }}?v=20260713" type="image/svg+xml">
<link rel="icon" href="{{ asset('favicon.ico') }}?v=20260713" sizes="any">
<link rel="icon" href="{{ asset('favicon-32x32.png') }}?v=20260713" type="image/png">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=20260713">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
