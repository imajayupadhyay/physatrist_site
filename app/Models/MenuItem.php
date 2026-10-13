<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * A website header menu item. Top-level items can have one level of children (dropdown / mega menu).
 * "service" items link to a service page and follow its title/slug automatically.
 */
class MenuItem extends Model
{
    public const TYPE_LINK = 'link';

    public const TYPE_SERVICE = 'service';

    public const STYLES = ['simple', 'mega'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'parent_id', 'type', 'service_id', 'label', 'url', 'description',
        'dropdown_style', 'open_in_new_tab', 'is_visible', 'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'open_in_new_tab' => 'boolean',
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // New items go to the end of their level.
        static::creating(function (MenuItem $item) {
            if (! $item->sort_order) {
                $item->sort_order = (int) static::where('parent_id', $item->parent_id)->max('sort_order') + 1;
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order')->orderBy('id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeTopLevel(Builder $query): void
    {
        $query->whereNull('parent_id');
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    public function isService(): bool
    {
        return $this->type === self::TYPE_SERVICE;
    }

    public function displayLabel(): string
    {
        return $this->label ?: ($this->service?->title ?? 'Untitled');
    }

    public function href(): ?string
    {
        return $this->isService()
            ? ($this->service ? '/services/'.$this->service->slug : null)
            : $this->url;
    }

    /**
     * Menu items with a broken target (unpublished/deleted service) are hidden on the website.
     */
    public function isLive(): bool
    {
        return $this->is_visible && (! $this->isService() || ($this->service && $this->service->is_published));
    }

    /**
     * The menu tree for the website header (visible, live items only).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public static function websiteTree(): Collection
    {
        return static::topLevel()
            ->ordered()
            ->with(['service', 'children.service'])
            ->get()
            ->filter->isLive()
            ->map(function (MenuItem $item) {
                $children = $item->children->filter->isLive()->map(fn (MenuItem $child) => [
                    'id' => $child->id,
                    'label' => $child->displayLabel(),
                    'href' => $child->href(),
                    'description' => $child->description ?: ($child->service ? Str::limit($child->service->summary, 90) : null),
                    'icon' => $child->service?->icon,
                    'audience' => $child->service?->audience,
                    'newTab' => $child->open_in_new_tab,
                ])->values();

                return [
                    'id' => $item->id,
                    'label' => $item->displayLabel(),
                    'href' => $item->href(),
                    'newTab' => $item->open_in_new_tab,
                    'style' => $children->isNotEmpty() ? $item->dropdown_style : null,
                    'children' => $children,
                ];
            })
            ->values();
    }
}
