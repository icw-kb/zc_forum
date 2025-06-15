<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class PluginGroup extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use Sluggable;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    /**
     * Get the plugins for the group.
     */
    public function plugins(): HasMany
    {
        return $this->hasMany(Plugin::class);
    }

    /**
     * Get the count of plugins in this group.
     */
    public function getPluginCountAttribute(): int
    {
        return $this->plugins()->count();
    }

    /**
     * Scope a query to order by plugin count.
     */
    public function scopeOrderByPluginCount($query, $direction = 'desc')
    {
        return $query->withCount('plugins')
            ->orderBy('plugins_count', $direction);
    }
}
