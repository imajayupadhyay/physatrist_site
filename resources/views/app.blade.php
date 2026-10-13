<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#2E4636">

        @include('partials.seo')

        {{-- Clinic structured data, from the site-wide contact details (Admin → Footer → Contact & social) --}}
        @if ($contact = $page['props']['contact'] ?? null)
            <script type="application/ld+json">{!! json_encode(array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'MedicalClinic',
                'name' => $contact['name'],
                'url' => url('/'),
                'telephone' => array_column($contact['phones'], 'tel'),
                'email' => $contact['email'],
                'address' => ['@type' => 'PostalAddress', 'streetAddress' => $contact['address'], 'addressCountry' => 'IN'],
                'hasMap' => $contact['mapsUrl'],
                'openingHours' => 'Mo-Sa 10:00-19:00',
                'sameAs' => array_values(array_filter([$contact['instagram'], $contact['youtube'], $contact['facebook'], $contact['linkedin']])),
                'employee' => ['@type' => 'Person', 'name' => 'Loveleena Sharma', 'jobTitle' => 'RCI Registered Clinical Psychologist'],
            ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
        @endif

        <link rel="icon" href="/favicon.ico" sizes="32x32">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">

        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
