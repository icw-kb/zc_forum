<?php

namespace App\Services;

use App\Models\Plugin;
use App\Models\PluginGroup;
use Illuminate\Support\Facades\Cache;

class PluginCacheService
{
    const CACHE_TTL = 3600; // 1 hour

    const FEATURED_PLUGINS_KEY = 'plugins.featured';

    const PLUGIN_GROUPS_KEY = 'plugins.groups';

    const PLUGIN_STATS_KEY = 'plugins.stats';

    /**
     * Get cached featured plugins.
     */
    public function getFeaturedPlugins(int $limit = 3): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            self::FEATURED_PLUGINS_KEY.".{$limit}",
            self::CACHE_TTL,
            function () use ($limit) {
                return Plugin::featured()
                    ->with(['group', 'versions'])
                    ->where('status', 'active')
                    ->take($limit)
                    ->get();
            }
        );
    }

    /**
     * Get cached plugin groups ordered by plugin count.
     */
    public function getPluginGroups(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            self::PLUGIN_GROUPS_KEY,
            self::CACHE_TTL,
            function () {
                return PluginGroup::orderByPluginCount()->get();
            }
        );
    }

    /**
     * Get cached plugin statistics for homepage/dashboard.
     */
    public function getPluginStatistics(): array
    {
        return Cache::remember(
            self::PLUGIN_STATS_KEY,
            self::CACHE_TTL,
            function () {
                return [
                    'total_plugins' => Plugin::where('status', 'active')->count(),
                    'total_downloads' => Plugin::sum('download_count'),
                    'total_views' => Plugin::sum('view_count'),
                    'featured_count' => Plugin::featured()->where('status', 'active')->count(),
                ];
            }
        );
    }

    /**
     * Get cache key for plugin listing based on filters.
     */
    public function getPluginListingCacheKey(array $filters = []): string
    {
        $key = 'plugins.listing';

        if (! empty($filters)) {
            $key .= '.'.md5(serialize($filters));
        }

        return $key;
    }

    /**
     * Cache plugin listing results.
     */
    public function cachePluginListing(string $cacheKey, $data, ?int $ttl = null): mixed
    {
        return Cache::remember($cacheKey, $ttl ?? self::CACHE_TTL, function () use ($data) {
            return is_callable($data) ? $data() : $data;
        });
    }

    /**
     * Clear all plugin-related caches.
     */
    public function clearAllCaches(): void
    {
        $patterns = [
            self::FEATURED_PLUGINS_KEY,
            self::PLUGIN_GROUPS_KEY,
            self::PLUGIN_STATS_KEY,
            'plugins.listing',
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
        
        // Clear additional known cache keys
        Cache::forget('plugins.listing');
        Cache::forget('plugins.featured');
        Cache::forget('plugins.statistics');
        
        // Clear paginated caches
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("plugins.listing.page.{$i}");
            Cache::forget("plugins.group.page.{$i}");
        }
    }

    /**
     * Clear caches when plugin data changes.
     */
    public function clearPluginCaches(): void
    {
        $this->clearAllCaches();
    }

    /**
     * Clear caches when group data changes.
     */
    public function clearGroupCaches(): void
    {
        Cache::forget(self::PLUGIN_GROUPS_KEY);

        // Clear listing caches that might be affected by group changes
        // Note: ArrayStore doesn't support pattern-based key retrieval
        // so we'll clear specific known cache keys
        Cache::forget('plugins.listing');
        Cache::forget('plugins.featured');
        Cache::forget('plugins.statistics');
        
        // Clear paginated listing caches (up to 10 pages)
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("plugins.listing.page.{$i}");
        }
    }

    /**
     * Get cache key for group-specific plugin listing.
     */
    public function getGroupPluginsCacheKey(int $groupId, array $filters = []): string
    {
        $key = "plugins.group.{$groupId}";

        if (! empty($filters)) {
            $key .= '.'.md5(serialize($filters));
        }

        return $key;
    }

    /**
     * Clear caches when plugin statistics change.
     */
    public function clearStatisticsCaches(): void
    {
        Cache::forget(self::PLUGIN_STATS_KEY);
        Cache::forget(self::FEATURED_PLUGINS_KEY);
        
        // Clear listing caches that might show different sorting
        Cache::forget('plugins.listing');
        
        // Clear paginated listing caches
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("plugins.listing.page.{$i}");
        }
    }
}
