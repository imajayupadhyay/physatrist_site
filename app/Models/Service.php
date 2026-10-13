<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * A service: its homepage card, its detail page (/services/{slug}) and its SEO settings.
 */
class Service extends Model
{
    /**
     * Icon keys available for homepage cards. The SVGs live in resources/js/Shared/serviceIcons.js.
     */
    public const ICONS = [
        'anxiety', 'ocd', 'adhd', 'autism', 'personality', 'relationships',
        'family', 'assessment', 'leaf', 'chat', 'brain', 'sparkle',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title', 'slug', 'concern_label', 'is_published', 'sort_order',
        'show_on_home', 'is_featured', 'icon', 'audience', 'summary',
        'hero_eyebrow', 'hero_title', 'intro', 'hero_image',
        'overview_heading', 'overview_body', 'issues_heading', 'issues',
        'approach_heading', 'approach', 'cta_heading', 'cta_text', 'faqs',
        'seo_title', 'meta_description', 'meta_keywords', 'canonical_url',
        'og_title', 'og_description', 'og_image', 'robots_index', 'robots_follow',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'show_on_home' => 'boolean',
            'is_featured' => 'boolean',
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
            'sort_order' => 'integer',
            'issues' => 'array',
            'approach' => 'array',
            'faqs' => 'array',
        ];
    }

    protected static function booted(): void
    {
        // New services go to the end of the list.
        static::creating(function (Service $service) {
            if (! $service->sort_order) {
                $service->sort_order = (int) static::max('sort_order') + 1;
            }
        });

        // Slug changed: keep the old URL working with a permanent redirect.
        static::updating(function (Service $service) {
            if ($service->isDirty('slug') && $service->getOriginal('slug')) {
                ServiceSlugRedirect::updateOrCreate(
                    ['old_slug' => $service->getOriginal('slug')],
                    ['service_id' => $service->id],
                );
            }
        });

        // The current slug must never be a redirect (to this or another service).
        static::saved(function (Service $service) {
            ServiceSlugRedirect::where('old_slug', $service->slug)->delete();
        });

        static::deleted(function (Service $service) {
            collect([$service->hero_image, $service->og_image])
                ->filter()
                ->each(fn ($path) => Storage::disk('public')->delete($path));
        });
    }

    public function redirects(): HasMany
    {
        return $this->hasMany(ServiceSlugRedirect::class);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Name used in the appointment form's "Area of concern" list.
     */
    public function concernName(): string
    {
        return $this->concern_label ?: $this->title;
    }

    public function url(): string
    {
        return route('services.show', $this->slug);
    }

    /**
     * Site-relative URL for an image on the public disk ("/storage/…"), so it works on any host.
     * (Storage::url() would bake in APP_URL.) Absolute URLs for SEO are made in App\Support\Seo.
     */
    public static function imageUrl(?string $path): ?string
    {
        return $path ? '/storage/'.ltrim($path, '/') : null;
    }
}
