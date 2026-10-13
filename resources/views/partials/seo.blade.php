{{--
    Server-rendered SEO tags from the page's `seo` prop, so crawlers and link previews
    see them without JavaScript. The `inertia` attributes match the head-keys in
    Components/Global/SeoHead.vue, which takes over these tags in the browser.
--}}
@php($seo = $page['props']['seo'] ?? null)
@if ($seo)
    <title inertia>{{ $seo['title'] }}</title>
    <meta inertia="description" name="description" content="{{ $seo['description'] }}">
    @if (! empty($seo['keywords']))
        <meta inertia="keywords" name="keywords" content="{{ $seo['keywords'] }}">
    @endif
    <meta inertia="robots" name="robots" content="{{ $seo['robots'] }}">
    <link inertia="canonical" rel="canonical" href="{{ $seo['canonical'] }}">
    <meta inertia="og:type" property="og:type" content="{{ $seo['og']['type'] }}">
    <meta inertia="og:site_name" property="og:site_name" content="{{ $seo['og']['site_name'] }}">
    <meta inertia="og:locale" property="og:locale" content="{{ $seo['og']['locale'] }}">
    <meta inertia="og:title" property="og:title" content="{{ $seo['og']['title'] }}">
    <meta inertia="og:description" property="og:description" content="{{ $seo['og']['description'] }}">
    <meta inertia="og:url" property="og:url" content="{{ $seo['og']['url'] }}">
    @if (! empty($seo['og']['image']))
        <meta inertia="og:image" property="og:image" content="{{ $seo['og']['image'] }}">
    @endif
    <meta inertia="twitter:card" name="twitter:card" content="summary_large_image">
    @foreach ($seo['jsonLd'] ?? [] as $schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @endforeach
@else
    <title inertia>{{ config('app.name', 'Laravel') }}</title>
@endif
