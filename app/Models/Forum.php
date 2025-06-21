<?php

namespace App\Models;

use App\Services\Traits\Restrictable;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;

class Forum extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use Auditable, HasFactory, Restrictable, Searchable, Sluggable, SoftDeletes;

    protected $guarded = [];

    public function forumGroup(): BelongsTo
    {
        return $this->belongsTo(ForumGroup::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ForumGroup::class, 'forum_group_id');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['name', 'id'],
            ],
        ];
    }

    public function sluggableEvent(): string
    {
        return SluggableObserver::SAVED;
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function latestPost()
    {
        return $this->hasOne(Post::class)->latest();
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            // Basic forum information
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            
            // Forum group context
            'forum_group_id' => $this->forum_group_id,
            'group_name' => $this->group?->name,
            'group_slug' => $this->group?->slug,
            'group_description' => $this->group?->description,
            
            // Content statistics
            'thread_count' => $this->threads()->count(),
            'post_count' => $this->posts()->count(),
            'open_thread_count' => $this->threads()->where('status', 'open')->count(),
            'pinned_thread_count' => $this->threads()->where('pinned', true)->count(),
            'solved_thread_count' => $this->threads()->whereHas('posts', function($q) {
                $q->where('is_accepted_answer', true);
            })->count(),
            
            // User engagement metrics
            'unique_thread_authors' => $this->threads()->distinct('user_id')->count('user_id'),
            'unique_post_authors' => $this->posts()->distinct('user_id')->count('user_id'),
            'total_thread_views' => $this->threads()->sum('views'),
            'total_post_likes' => $this->posts()->withCount('likes')->get()->sum('likes_count'),
            'active_users_last_30_days' => $this->posts()
                ->where('created_at', '>=', now()->subDays(30))
                ->distinct('user_id')
                ->count('user_id'),
            
            // Activity metrics
            'latest_post_at' => $this->latestPost?->created_at?->timestamp,
            'latest_thread_at' => $this->threads()->latest('created_at')->first()?->created_at?->timestamp,
            'days_since_last_post' => $this->latestPost?->created_at?->diffInDays(now()) ?? 365,
            'days_since_last_thread' => $this->threads()->latest('created_at')->first()?->created_at?->diffInDays(now()) ?? 365,
            
            // Content quality indicators
            'avg_posts_per_thread' => $this->threads()->count() > 0 ? round($this->posts()->count() / $this->threads()->count(), 2) : 0,
            'avg_thread_views' => $this->threads()->count() > 0 ? round($this->threads()->sum('views') / $this->threads()->count(), 2) : 0,
            'acceptance_rate' => $this->threads()->count() > 0 ? round(($this->solved_thread_count / $this->threads()->count()) * 100, 2) : 0,
            
            // Popular topics/keywords
            'popular_thread_titles' => $this->threads()
                ->where('status', 'open')
                ->orderBy('views', 'desc')
                ->limit(10)
                ->pluck('title')
                ->toArray(),
            'common_keywords' => $this->extractForumKeywords(),
            
            // Temporal data
            'created_at' => $this->created_at?->timestamp,
            'updated_at' => $this->updated_at?->timestamp,
            'age_in_days' => $this->created_at?->diffInDays(now()) ?? 0,
            
            // Activity trends (last 7 days vs previous 7 days)
            'recent_activity_trend' => $this->calculateActivityTrend(),
        ];
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->status === 'open' && !$this->trashed();
    }

    // Helper methods for forum analysis
    private function extractForumKeywords(): array
    {
        // Get keywords from recent thread titles and first posts
        $recentThreads = $this->threads()
            ->where('created_at', '>=', now()->subDays(90))
            ->with('posts')
            ->get();
        
        $content = $recentThreads->pluck('title')->implode(' ') . ' ' .
                   $recentThreads->map(function($thread) {
                       return $thread->posts->first()?->content ?? '';
                   })->implode(' ');
        
        $content = strip_tags($content);
        
        // Extract domain-specific keywords
        preg_match_all('/\b(?:zen-?cart|plugin|module|payment|shipping|admin|customer|order|product|category|tax|email|template|override|function|error|bug|fix|install|upgrade|version|php|mysql|sql|css|html|javascript|jquery|security|performance|optimization|seo|ssl|backup|database|configuration|attribute|discount|coupon|layout|checkout|cart|mobile|responsive)\b/i', $content, $matches);
        
        $keywords = array_map('strtolower', $matches[0] ?? []);
        $keywordCounts = array_count_values($keywords);
        arsort($keywordCounts);
        
        return array_keys(array_slice($keywordCounts, 0, 20)); // Top 20 keywords
    }

    private function calculateActivityTrend(): float
    {
        $recent7Days = $this->posts()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        $previous7Days = $this->posts()
            ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();
        
        if ($previous7Days === 0) {
            return $recent7Days > 0 ? 1.0 : 0.0;
        }
        
        return round(($recent7Days - $previous7Days) / $previous7Days, 2);
    }
}
