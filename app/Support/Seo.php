<?php

namespace App\Support;

use App\Models\Service;
use Illuminate\Support\Str;

/**
 * Builds the `seo` prop. It is rendered server-side by resources/views/partials/seo.blade.php
 * (for crawlers and link previews) and client-side by Components/Global/SeoHead.vue.
 */
class Seo
{
    /**
     * @return array<string, mixed>
     */
    public static function make(
        string $title,
        ?string $description = null,
        ?string $canonical = null,
        ?string $keywords = null,
        bool $index = true,
        bool $follow = true,
        ?string $ogTitle = null,
        ?string $ogDescription = null,
        ?string $ogImage = null,
        string $ogType = 'website',
        array $jsonLd = [],
    ): array {
        $description = Str::limit(trim((string) ($description ?: config('seo.default_description'))), 300, '…');
        $canonical = $canonical ?: url()->current();

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords ?: null,
            'canonical' => $canonical,
            'robots' => ($index ? 'index' : 'noindex').', '.($follow ? 'follow' : 'nofollow'),
            'og' => [
                'title' => $ogTitle ?: $title,
                'description' => $ogDescription ?: $description,
                'image' => self::absolute($ogImage ?: config('seo.default_image')),
                'url' => $canonical,
                'type' => $ogType,
                'site_name' => config('seo.site_name'),
                'locale' => config('seo.locale'),
            ],
            'jsonLd' => $jsonLd,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function home(): array
    {
        $seo = HomeContent::get('seo');

        return self::make(
            title: $seo['title'] ?: config('seo.default_title'),
            description: $seo['description'],
            canonical: url('/'),
            keywords: $seo['keywords'],
            ogTitle: $seo['og_title'],
            ogDescription: $seo['og_description'],
            ogImage: $seo['og_image'] ?: null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function service(Service $service): array
    {
        $title = $service->seo_title ?: $service->title.' · '.config('seo.title_suffix');
        $description = $service->meta_description ?: $service->intro;
        $canonical = $service->canonical_url ?: $service->url();

        $jsonLd = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'Service',
                'name' => $service->title,
                'description' => Str::limit(strip_tags((string) $description), 300, '…'),
                'url' => $canonical,
                'serviceType' => $service->title,
                'audience' => $service->audience ? ['@type' => 'Audience', 'audienceType' => $service->audience] : null,
                'provider' => [
                    '@type' => 'MedicalClinic',
                    'name' => config('seo.site_name'),
                    'url' => url('/'),
                ],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Services', 'item' => url('/#services')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $service->title, 'item' => $service->url()],
                ],
            ],
        ];

        if (! empty($service->faqs)) {
            $jsonLd[] = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => collect($service->faqs)->map(fn ($faq) => [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => self::plainText($faq['answer'])],
                ])->all(),
            ];
        }

        return self::make(
            title: $title,
            description: $description,
            canonical: $canonical,
            keywords: $service->meta_keywords,
            index: $service->robots_index,
            follow: $service->robots_follow,
            ogTitle: $service->og_title,
            ogDescription: $service->og_description,
            ogImage: Service::imageUrl($service->og_image) ?: Service::imageUrl($service->hero_image),
            ogType: 'article',
            jsonLd: array_map(fn ($item) => array_filter($item, fn ($value) => $value !== null), $jsonLd),
        );
    }

    /**
     * "[link text](url)" → "link text"
     */
    public static function plainText(string $text): string
    {
        return preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $text);
    }

    private static function absolute(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        return Str::startsWith($url, ['http://', 'https://']) ? $url : url($url);
    }
}
