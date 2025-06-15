<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'plugin_id',
        'user_id',
        'action',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the plugin that owns the statistic.
     */
    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }

    /**
     * Get the user that created the statistic.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include view statistics.
     */
    public function scopeViews($query)
    {
        return $query->where('action', 'view');
    }

    /**
     * Scope a query to only include download statistics.
     */
    public function scopeDownloads($query)
    {
        return $query->where('action', 'download');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to group by day.
     */
    public function scopeGroupByDay($query)
    {
        return $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date');
    }

    /**
     * Scope a query to get unique views/downloads by IP.
     */
    public function scopeUniqueByIp($query)
    {
        return $query->select('plugin_id', 'action', 'ip_address')
            ->distinct();
    }
}
