<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Arr;

/**
 * Homepage content, one record per section ("home.{section}" in `settings`),
 * merged over the defaults in config/home.php. Managed in Admin → Home page.
 */
class HomeContent
{
    /**
     * Editable sections, in homepage order. key => admin label.
     */
    public const SECTIONS = [
        'hero' => 'Hero slider',
        'trust' => 'Trust strip',
        'about' => 'About the psychologist',
        'assessment' => 'Assessments',
        'steps' => 'First steps',
        'space' => 'Our space',
        'videos' => 'Videos',
        'testimonials' => 'Testimonials',
        'faq' => 'FAQ',
        'seo' => 'SEO & sharing',
    ];

    /**
     * Image fields per section (dot paths, `*` = every item of a list).
     * Each has an optional "{field}_file" upload in the request.
     */
    public const IMAGES = [
        'hero' => ['slides.*.image', 'card.photo'],
        'about' => ['image'],
        'space' => ['photos.*.image'],
        'videos' => ['channel_image', 'reels.*.image'],
        'seo' => ['og_image'],
    ];

    /**
     * @return array<string, mixed>
     */
    public static function get(string $section): array
    {
        return self::merge(config("home.{$section}"), Setting::get("home.{$section}", []));
    }

    public static function put(string $section, array $data): void
    {
        Setting::put("home.{$section}", $data);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return collect(array_keys(self::SECTIONS))->mapWithKeys(fn ($section) => [$section => self::get($section)])->all();
    }

    /**
     * Saved values win; nested settings (e.g. hero.card) are merged key by key so new
     * default keys still apply. Lists are replaced as a whole.
     */
    private static function merge(array $defaults, array $saved): array
    {
        foreach ($saved as $key => $value) {
            $defaults[$key] = is_array($value) && isset($defaults[$key]) && is_array($defaults[$key]) && Arr::isAssoc($defaults[$key]) && Arr::isAssoc($value)
                ? self::merge($defaults[$key], $value)
                : $value;
        }

        return $defaults;
    }

    /**
     * "https://youtu.be/kSVMb10Rj6I", ".../watch?v=…", ".../shorts/…", ".../embed/…" or a bare ID → the 11-character ID.
     */
    public static function youtubeId(?string $value): ?string
    {
        $value = trim((string) $value);

        if (preg_match('~^[A-Za-z0-9_-]{11}$~', $value)) {
            return $value;
        }

        if (preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/))([A-Za-z0-9_-]{11})~', $value, $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * Every uploaded-file URL ("/storage/home/…") inside a section, for cleaning up replaced images.
     *
     * @return list<string>
     */
    public static function uploadedFiles(array $data): array
    {
        return collect(Arr::flatten($data))
            ->filter(fn ($value) => is_string($value) && str_starts_with($value, '/storage/home/'))
            ->values()
            ->all();
    }
}
