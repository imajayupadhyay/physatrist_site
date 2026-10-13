<?php

namespace App\Support;

use App\Models\Service;
use App\Models\Setting;

/**
 * Website footer (Admin → Footer).
 */
class Footer
{
    /**
     * @return array<string, mixed>
     */
    public static function settings(): array
    {
        return array_merge(config('content.footer'), Setting::get('footer', []));
    }

    /**
     * The `footer` prop shared with every public page.
     *
     * @return array<string, mixed>
     */
    public static function forWebsite(): array
    {
        $f = self::settings();

        $services = null; // loaded once, only if a "services" column exists

        return [
            'logo' => [
                'url' => Service::imageUrl($f['logo']),
                'alt' => $f['logo_alt'],
                'height' => (int) $f['logo_height'],
                'name' => $f['brand_name'],
                'tagline' => $f['brand_tagline'],
            ],
            'aboutText' => $f['about_text'],
            'credentials' => $f['credentials_name'] || $f['credentials_text'] ? [
                'name' => $f['credentials_name'],
                'lines' => array_values(array_filter(array_map('trim', explode("\n", (string) $f['credentials_text'])))),
            ] : null,
            'showSocials' => (bool) $f['show_socials'],
            'columns' => collect($f['columns'])->map(function (array $column) use (&$services) {
                if ($column['type'] === 'services') {
                    $services ??= Service::published()->ordered()->get(['title', 'slug']);

                    $links = $services->take($column['limit'] ?: 6)->map(fn (Service $service) => [
                        'label' => $service->title,
                        'href' => '/services/'.$service->slug,
                        'newTab' => false,
                    ]);
                } else {
                    $links = collect($column['links'] ?? [])->map(fn ($link) => [
                        'label' => $link['label'],
                        'href' => $link['url'],
                        'newTab' => (bool) ($link['new_tab'] ?? false),
                    ]);
                }

                return ['heading' => $column['heading'], 'links' => $links->values()->all()];
            })->values()->all(),
            'contactHeading' => $f['contact_heading'],
            'showWebsite' => (bool) $f['show_website'],
            'showHours' => (bool) $f['show_hours'],
            'helpline' => $f['helpline_visible'] ? $f['helpline_text'] : null,
            'copyright' => str_replace('{year}', (string) now()->year, (string) $f['copyright_text']),
            'bottomText' => $f['bottom_text'],
        ];
    }
}
