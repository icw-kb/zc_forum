<?php

namespace App\Models;

use App\Services\PluginCacheService;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Contracts\Auditable;

class PluginGroup extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use Searchable;
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

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear caches when group data changes
        static::saved(function () {
            app(PluginCacheService::class)->clearGroupCaches();
        });

        static::deleted(function () {
            app(PluginCacheService::class)->clearGroupCaches();
        });
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            // Basic group information
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'plugins_count' => $this->plugins()->count(),
            'active_plugins_count' => $this->plugins()->where('status', 'open')->count(),
            'featured_plugins_count' => $this->plugins()->where('is_featured', true)->count(),
            'total_downloads' => $this->plugins()->sum('download_count'),
            'total_views' => $this->plugins()->sum('view_count'),
            'popular_plugin_names' => $this->plugins()
                ->where('status', 'open')
                ->orderBy('download_count', 'desc')
                ->limit(5)
                ->pluck('name')
                ->toArray(),
            'latest_plugin_date' => $this->plugins()
                ->where('status', 'open')
                ->latest('created_at')
                ->first()?->created_at?->timestamp,
            'created_at' => $this->created_at?->timestamp,
            'updated_at' => $this->updated_at?->timestamp,
        ];
    }
}
