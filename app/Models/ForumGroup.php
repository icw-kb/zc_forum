<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;

class ForumGroup extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use Auditable, HasFactory, Searchable, Sluggable, SoftDeletes;

    protected $guarded = [];

    public function toSearchableArray(): array
    {
        return [
            // Basic group information
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
            
            // Forum statistics
            'forum_count' => $this->forums()->count(),
            'active_forum_count' => $this->forums()->where('status', 'open')->count(),
            'popular_forum_names' => $this->forums()
                ->withCount('threads')
                ->orderBy('threads_count', 'desc')
                ->limit(5)
                ->pluck('name')
                ->toArray(),
            
            // Aggregate content statistics
            'total_threads' => $this->calculateTotalThreads(),
            'total_posts' => $this->calculateTotalPosts(),
            'total_views' => $this->calculateTotalViews(),
            'total_likes' => $this->calculateTotalLikes(),
            'open_threads' => $this->calculateOpenThreads(),
            'solved_threads' => $this->calculateSolvedThreads(),
            'pinned_threads' => $this->calculatePinnedThreads(),
            
            // User engagement metrics
            'unique_thread_authors' => $this->calculateUniqueThreadAuthors(),
            'unique_post_authors' => $this->calculateUniquePostAuthors(),
            'active_users_last_30_days' => $this->calculateActiveUsers(30),
            'active_users_last_7_days' => $this->calculateActiveUsers(7),
            
            // Activity metrics
            'latest_post_at' => $this->getLatestPostTimestamp(),
            'latest_thread_at' => $this->getLatestThreadTimestamp(),
            'most_active_forum' => $this->getMostActiveForumName(),
            'avg_posts_per_thread' => $this->calculateAvgPostsPerThread(),
            'avg_views_per_thread' => $this->calculateAvgViewsPerThread(),
            
            // Quality indicators
            'acceptance_rate' => $this->calculateAcceptanceRate(),
            'engagement_score' => $this->calculateEngagementScore(),
            'activity_trend' => $this->calculateActivityTrend(),
            
            // Content themes/keywords
            'dominant_topics' => $this->extractDominantTopics(),
            'common_keywords' => $this->extractCommonKeywords(),
            
            // Temporal data
            'created_at' => $this->created_at?->timestamp,
            'updated_at' => $this->updated_at?->timestamp,
            'age_in_days' => $this->created_at?->diffInDays(now()) ?? 0,
            'days_since_last_activity' => $this->getLatestPostTimestamp() ? 
                now()->diffInDays(\Carbon\Carbon::createFromTimestamp($this->getLatestPostTimestamp())) : 365,
        ];
    }

    public static function getGroupsForUser()
    {
        return ForumGroup::all();
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

    public function forums(): HasMany
    {
        return $this->hasMany(Forum::class);
    }

    // Helper methods for forum group analysis
    private function calculateTotalThreads(): int
    {
        return $this->forums()->withCount('threads')->get()->sum('threads_count');
    }

    private function calculateTotalPosts(): int
    {
        return $this->forums()->withCount('posts')->get()->sum('posts_count');
    }

    private function calculateTotalViews(): int
    {
        return Thread::whereIn('forum_id', $this->forums->pluck('id'))->sum('views');
    }

    private function calculateTotalLikes(): int
    {
        $forumIds = $this->forums->pluck('id');
        return Post::whereIn('forum_id', $forumIds)->withCount('likes')->get()->sum('likes_count');
    }

    private function calculateOpenThreads(): int
    {
        return Thread::whereIn('forum_id', $this->forums->pluck('id'))
            ->where('status', 'open')
            ->count();
    }

    private function calculateSolvedThreads(): int
    {
        return Thread::whereIn('forum_id', $this->forums->pluck('id'))
            ->whereHas('posts', function($q) {
                $q->where('is_accepted_answer', true);
            })
            ->count();
    }

    private function calculatePinnedThreads(): int
    {
        return Thread::whereIn('forum_id', $this->forums->pluck('id'))
            ->where('pinned', true)
            ->count();
    }

    private function calculateUniqueThreadAuthors(): int
    {
        return Thread::whereIn('forum_id', $this->forums->pluck('id'))
            ->distinct('user_id')
            ->count('user_id');
    }

    private function calculateUniquePostAuthors(): int
    {
        return Post::whereIn('forum_id', $this->forums->pluck('id'))
            ->distinct('user_id')
            ->count('user_id');
    }

    private function calculateActiveUsers(int $days): int
    {
        return Post::whereIn('forum_id', $this->forums->pluck('id'))
            ->where('created_at', '>=', now()->subDays($days))
            ->distinct('user_id')
            ->count('user_id');
    }

    private function getLatestPostTimestamp(): ?int
    {
        $latestPost = Post::whereIn('forum_id', $this->forums->pluck('id'))
            ->latest('created_at')
            ->first();
        
        return $latestPost?->created_at?->timestamp;
    }

    private function getLatestThreadTimestamp(): ?int
    {
        $latestThread = Thread::whereIn('forum_id', $this->forums->pluck('id'))
            ->latest('created_at')
            ->first();
        
        return $latestThread?->created_at?->timestamp;
    }

    private function getMostActiveForumName(): ?string
    {
        return $this->forums()
            ->withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->first()?->name;
    }

    private function calculateAvgPostsPerThread(): float
    {
        $totalThreads = $this->calculateTotalThreads();
        $totalPosts = $this->calculateTotalPosts();
        
        return $totalThreads > 0 ? round($totalPosts / $totalThreads, 2) : 0;
    }

    private function calculateAvgViewsPerThread(): float
    {
        $totalThreads = $this->calculateTotalThreads();
        $totalViews = $this->calculateTotalViews();
        
        return $totalThreads > 0 ? round($totalViews / $totalThreads, 2) : 0;
    }

    private function calculateAcceptanceRate(): float
    {
        $totalThreads = $this->calculateTotalThreads();
        $solvedThreads = $this->calculateSolvedThreads();
        
        return $totalThreads > 0 ? round(($solvedThreads / $totalThreads) * 100, 2) : 0;
    }

    private function calculateEngagementScore(): float
    {
        $totalViews = $this->calculateTotalViews();
        $totalLikes = $this->calculateTotalLikes();
        $totalPosts = $this->calculateTotalPosts();
        
        // Weighted engagement score
        return round(($totalViews * 0.1) + ($totalLikes * 2) + ($totalPosts * 0.5), 2);
    }

    private function calculateActivityTrend(): float
    {
        $forumIds = $this->forums->pluck('id');
        
        $recent7Days = Post::whereIn('forum_id', $forumIds)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        $previous7Days = Post::whereIn('forum_id', $forumIds)
            ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();
        
        if ($previous7Days === 0) {
            return $recent7Days > 0 ? 1.0 : 0.0;
        }
        
        return round(($recent7Days - $previous7Days) / $previous7Days, 2);
    }

    private function extractDominantTopics(): array
    {
        $forumNames = $this->forums->pluck('name')->toArray();
        $popularThreads = Thread::whereIn('forum_id', $this->forums->pluck('id'))
            ->orderBy('views', 'desc')
            ->limit(20)
            ->pluck('title')
            ->toArray();
        
        $content = implode(' ', array_merge($forumNames, $popularThreads));
        
        // Extract major topic categories
        preg_match_all('/\b(?:payment|shipping|admin|product|order|customer|plugin|module|addon|template|theme|customization|development|installation|upgrade|configuration|performance|security|seo|mobile|responsive|database|backup|troubleshooting|support|bug|error|fix)\b/i', $content, $matches);
        
        $topics = array_map('strtolower', $matches[0] ?? []);
        $topicCounts = array_count_values($topics);
        arsort($topicCounts);
        
        return array_keys(array_slice($topicCounts, 0, 10));
    }

    private function extractCommonKeywords(): array
    {
        // Similar to extractDominantTopics but more comprehensive
        $recentThreads = Thread::whereIn('forum_id', $this->forums->pluck('id'))
            ->where('created_at', '>=', now()->subDays(30))
            ->limit(100)
            ->pluck('title')
            ->implode(' ');
        
        preg_match_all('/\b(?:zen-?cart|zencart|plugin|module|payment|shipping|admin|customer|order|product|category|tax|email|template|override|function|error|bug|fix|install|upgrade|version|php|mysql|sql|css|html|javascript|jquery|bootstrap|responsive|mobile|seo|ssl|https|database|backup|security|performance|optimization|cache|session|cookie|debug|log|configuration|setting|attribute|option|discount|coupon|zone|country|language|currency|layout|sidebar|header|footer|navigation|menu|checkout|cart|wishlist|compare|review|rating)\b/i', $recentThreads, $matches);
        
        $keywords = array_map('strtolower', $matches[0] ?? []);
        $keywordCounts = array_count_values($keywords);
        arsort($keywordCounts);
        
        return array_keys(array_slice($keywordCounts, 0, 15));
    }
}
