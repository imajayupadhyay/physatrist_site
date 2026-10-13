<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * An appointment request submitted from the website's "Book an appointment" form.
 */
class Lead extends Model
{
    public const STATUS_NEW = 'new';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'concern',
        'preferred_date',
        'mode',
        'message',
        'status',
        'notes',
        'source',
        'ip_address',
        'user_agent',
    ];

    /**
     * The model's default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => self::STATUS_NEW,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
        ];
    }

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return array_keys(config('leads.statuses'));
    }

    /**
     * Options for the appointment form's "Area of concern": one per published service.
     *
     * @return list<string>
     */
    public static function concernOptions(): array
    {
        $services = Service::published()->ordered()->get()->map->concernName();

        return collect(['Not sure yet'])
            ->merge($services)
            ->push('Other')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Props for any page that renders the appointment form.
     *
     * @return array{concerns: list<string>, modes: list<string>}
     */
    public static function formOptions(): array
    {
        return [
            'concerns' => static::concernOptions(),
            'modes' => config('leads.modes'),
        ];
    }

    /**
     * Search by name, phone or email.
     */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $query->where(fn (Builder $query) => $query
            ->where('name', 'like', "%{$term}%")
            ->orWhere('phone', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%"));
    }
}
