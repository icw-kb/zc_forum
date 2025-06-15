<?php

namespace App\Models;

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
        'github_url',
        'status',
        'view_count',
        'download_count',
        'featured',
    ];

    protected $casts = [
        'featured' => 'boolean',
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
        return $query->where('featured', true);
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
    }

    /**
     * Increment the download count for the plugin.
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    /**
     * Record a view statistic.
     */
    public function recordView(?int $userId = null, ?string $ipAddress = null, ?string $userAgent = null): void
    {
        $this->statistics()->create([
            'user_id' => $userId,
            'action' => 'view',
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
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
}
