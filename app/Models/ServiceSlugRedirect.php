<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A previous slug of a service; /services/{old_slug} 301-redirects to the current URL.
 */
class ServiceSlugRedirect extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['old_slug', 'service_id'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
