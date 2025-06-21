<?php

namespace App\Models;

use App\Services\PluginCacheService;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;

class Plugin extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use Auditable, HasFactory, Searchable, Sluggable;

    protected $fillable = [
        'plugin_group_id',
        'name',
        'slug',
        'description',
         'tags',
        'user_id',
        'status',
        'view_count',
        'download_count',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'tags' => 'array',
        'view_count' => 'integer',
        'download_count' => 'integer',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    /**
     * Get the plugin group that owns the plugin.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(PluginGroup::class, 'plugin_group_id');
    }

    /**
     * Get the versions for the plugin.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(\App\Models\PluginVersion::class);
    }

    /**
     * Get the statistics for the plugin.
     */
    public function statistics(): HasMany
    {
        return $this->hasMany(PluginStatistic::class);
    }

    /**
     * Scope a query to only include featured plugins.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include plugins of a given group.
     */
    public function scopeByGroup(Builder $query, $groupId): Builder
    {
        return $query->where('plugin_group_id', $groupId);
    }

    /**
     * Scope a query to include plugin statistics.
     */
    public function scopeWithStatistics(Builder $query): Builder
    {
        return $query->withCount([
            'statistics as views_count' => function ($query) {
                $query->where('action', 'view');
            },
            'statistics as downloads_count' => function ($query) {
                $query->where('action', 'download');
            },
        ]);
    }

    /**
     * Scope a query to order by most downloaded.
     */
    public function scopeMostDownloaded(Builder $query): Builder
    {
        return $query->orderBy('download_count', 'desc');
    }

    /**
     * Scope a query to order by most viewed.
     */
    public function scopeMostViewed(Builder $query): Builder
    {
        return $query->orderBy('view_count', 'desc');
    }

    /**
     * Increment the view count for the plugin.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
        app(PluginCacheService::class)->clearStatisticsCaches();
    }

    /**
     * Increment the download count for the plugin.
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
        app(PluginCacheService::class)->clearStatisticsCaches();
        \Cache::forget('plugins.top-downloaded');
    }

    /**
     * Record a view statistic.
     */
    public function recordView(?int $userId = null, ?string $ipAddress = null, ?string $userAgent = null): void
    {
        $ipAddress = $ipAddress ?? request()->ip();
        $userAgent = $userAgent ?? request()->userAgent();

        // Check if this user/IP has viewed this plugin recently (within 1 hour)
        $recentView = $this->statistics()
            ->where('action', 'view')
            ->where('ip_address', $ipAddress)
            ->when($userId, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->where('created_at', '>', now()->subHour())
            ->exists();

        if ($recentView) {
            return; // Don't record duplicate views
        }

        $this->statistics()->create([
            'user_id' => $userId,
            'action' => 'view',
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        $this->incrementViewCount();
    }

    /**
     * Record a download statistic.
     */
    public function recordDownload(?int $userId = null, ?string $ipAddress = null, ?string $userAgent = null): void
    {
        $this->statistics()->create([
            'user_id' => $userId,
            'action' => 'download',
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);

        $this->incrementDownloadCount();
    }

    /**
     * Get the latest version of the plugin.
     */
    public function getLatestVersionAttribute()
    {
        return $this->versions()->latest('version')->first();
    }

    /**
     * Check if the plugin has any versions.
     */
    public function hasVersions(): bool
    {
        return $this->versions()->exists();
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'view_count' => $this->view_count,
            'download_count' => $this->download_count,
            'plugin_group_id' => $this->plugin_group_id,
            'group_name' => $this->group?->name,
            'created_at' => $this->created_at?->timestamp,
            'updated_at' => $this->updated_at?->timestamp,
        ];
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear caches when plugin data changes
        static::saved(function () {
            app(PluginCacheService::class)->clearPluginCaches();
            \Cache::forget('plugins.featured-sidebar');
        });

        static::deleted(function () {
            app(PluginCacheService::class)->clearPluginCaches();
            \Cache::forget('plugins.featured-sidebar');
        });
    }
}
