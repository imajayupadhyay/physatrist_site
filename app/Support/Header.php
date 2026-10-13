<?php

namespace App\Support;

use App\Models\MenuItem;
use App\Models\Service;
use App\Models\Setting;

/**
 * Website header: settings (logo, CTA, mega-menu promo) + the menu tree.
 */
class Header
{
    /**
     * Saved settings merged over the defaults in config/content.php.
     *
     * @return array<string, mixed>
     */
    public static function settings(): array
    {
        return array_merge(config('content.header'), Setting::get('header', []));
    }

    /**
     * The `header` prop shared with every public page.
     *
     * @return array<string, mixed>
     */
    public static function forWebsite(): array
    {
        $settings = self::settings();

        return [
            'logo' => [
                'url' => Service::imageUrl($settings['logo']),
                'alt' => $settings['logo_alt'],
                'height' => (int) $settings['logo_height'],
                'name' => $settings['brand_name'],
                'tagline' => $settings['brand_tagline'],
            ],
            'cta' => $settings['cta_visible'] ? [
                'label' => $settings['cta_label'],
                'href' => $settings['cta_url'],
                'newTab' => (bool) $settings['cta_new_tab'],
            ] : null,
            'megaPromo' => $settings['mega_promo_visible'] ? [
                'title' => $settings['mega_promo_title'],
                'text' => $settings['mega_promo_text'],
                'ctaLabel' => $settings['mega_promo_cta_label'],
                'ctaHref' => $settings['mega_promo_cta_url'],
            ] : null,
            'menu' => MenuItem::websiteTree(),
        ];
    }
}
