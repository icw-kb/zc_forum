# Scout Search Implementation Documentation

## Overview
This document details the current Laravel Scout search implementation in the ZC Forum application. It serves as a reference for understanding how search is configured and what data is being indexed.

**Last Updated**: 2025-06-21

## 1. Scout Configuration

### General Settings
- **Default Driver**: Configured via `SCOUT_DRIVER` env variable (defaults to Algolia)
- **Index Prefix**: Optional via `SCOUT_PREFIX` env variable
- **Queue**: Optional queuing for sync operations via `SCOUT_QUEUE` (default: false)
- **After Commit**: Disabled - syncs happen immediately, not after DB transactions
- **Soft Deletes**: Disabled - soft deleted records are not kept in search index
- **Chunk Size**: 500 records for bulk import/export operations

### Driver Support
The application is configured to support:
- Algolia
- Meilisearch (with custom configuration)
- Typesense
- Database driver
- Collection driver
- Null driver

## 2. Models with Searchable Trait

The following models implement Laravel Scout's `Searchable` trait:

| Model | Custom Search Config | Status |
|-------|---------------------|---------|
| `Plugin` | ✅ Full implementation | Active |
| `PluginVersion` | ✅ Enhanced implementation | Active |
| `PluginGroup` | ✅ New implementation | Recommended |
| `PluginStatistic` | ✅ New implementation | Optional |
| `ZencartVersion` | ✅ New implementation | Optional |
| `Thread` | ❌ Default only | Active |
| `Post` | ❌ Default only | Active |
| `Forum` | ❌ Default only | Active |
| `ForumGroup` | ❌ Default only | Active |

## 3. Plugin Model Search Implementation

### Indexed Fields
The `Plugin` model implements a custom `toSearchableArray()` method that indexes:

```php
[
    'id' => $this->id,
    'name' => $this->name,
    'slug' => $this->slug,
    'description' => $this->description,
    'tags' => $this->tags,  // Array field for categorization
    'status' => $this->status,
    'is_featured' => $this->is_featured,
    'view_count' => $this->view_count,
    'download_count' => $this->download_count,
    'plugin_group_id' => $this->plugin_group_id,
    'group_name' => $this->group?->name,
    'created_at' => $this->created_at?->timestamp,
    'updated_at' => $this->updated_at?->timestamp,
]
```

**Note**: The model was recently updated to include a `tags` field (array) but `github_url` was removed from the searchable array.

### Search Conditions
- **Searchable Condition**: Only plugins with `status === 'open'` are indexed
- **Implementation**: Via `shouldBeSearchable()` method

## 4. Meilisearch-Specific Configuration

### Plugin Index Configuration

#### Searchable Attributes
These fields are used for text search:
- `name`
- `description`
- `group_name`
- `tags` (array field)

#### Filterable Attributes
These fields can be used in filter queries:
- `status`
- `is_featured`
- `plugin_group_id`
- `group_name`
- `tags` (for tag-based filtering)

#### Sortable Attributes
These fields can be used for sorting results:
- `download_count`
- `view_count`
- `created_at`
- `updated_at`

#### Ranking Rules
Results are ranked in this order:
1. `words` - Number of query words found
2. `typo` - Fewer typos rank higher
3. `proximity` - Words closer together rank higher
4. `attribute` - Matches in more important attributes rank higher
5. `sort` - Custom sort order if specified
6. `exactness` - Exact matches rank higher
7. `download_count:desc` - More downloads rank higher
8. `view_count:desc` - More views rank higher

#### Additional Settings
- **Distinct Attribute**: `id` (prevents duplicate results)
- **Typo Tolerance**: 
  - Enabled by default
  - One typo allowed for words ≥ 5 characters
  - Two typos allowed for words ≥ 9 characters

## 5. Other Searchable Models

### Forum-Related Models
- **Forum**: Uses default Laravel Scout implementation
- **ForumGroup**: Uses default Laravel Scout implementation
- **Thread**: Uses default Laravel Scout implementation
- **Post**: Uses default Laravel Scout implementation
- **PluginVersion**: Uses default Laravel Scout implementation

These models will index all their `$fillable` attributes by default.

## 6. Current Implementation for Models Without toSearchableArray

Since these models currently use the default Scout implementation, here are the recommended `toSearchableArray()` methods to add:

### Thread Model Implementation (Enhanced)
```php
// app/Models/Thread.php
public function toSearchableArray(): array
{
    return [
        // Basic thread information
        'id' => $this->id,
        'title' => $this->title,
        'slug' => $this->slug,
        'status' => $this->status,
        
        // User context
        'user_id' => $this->user_id,
        'user_name' => $this->user?->name,
        'user_post_count' => $this->user?->posts()->count() ?? 0,
        'user_thread_count' => $this->user?->threads()->count() ?? 0,
        
        // Forum hierarchy
        'forum_id' => $this->forum_id,
        'forum_name' => $this->forum?->name,
        'forum_slug' => $this->forum?->slug,
        'forum_group_id' => $this->forum?->forum_group_id,
        'forum_group_name' => $this->forum?->group?->name,
        'forum_group_slug' => $this->forum?->group?->slug,
        
        // Engagement metrics
        'view_count' => $this->views ?? 0,
        'post_count' => $this->posts()->count(),
        'subscriber_count' => $this->subscribers()->count(),
        'unique_participants' => $this->posts()->distinct('user_id')->count('user_id'),
        
        // Content quality indicators
        'is_pinned' => $this->pinned,
        'total_likes' => $this->posts()->withCount('likes')->get()->sum('likes_count'),
        'avg_post_length' => $this->posts()->selectRaw('AVG(LENGTH(content)) as avg_length')->first()?->avg_length ?? 0,
        
        // Activity and recency
        'created_at' => $this->created_at?->timestamp,
        'updated_at' => $this->updated_at?->timestamp,
        'latest_post_at' => $this->latestPost?->created_at?->timestamp,
        'days_since_created' => $this->created_at?->diffInDays(now()) ?? 0,
        'days_since_activity' => $this->latestPost?->created_at?->diffInDays(now()) ?? 
                                 $this->created_at?->diffInDays(now()) ?? 0,
        
        // First post content for context (excerpt)
        'first_post_excerpt' => $this->posts()->oldest()->first()?->content ? 
            Str::limit(strip_tags($this->posts()->oldest()->first()->content), 200) : '',
        
        // Popular tags/keywords (if implemented)
        'tags' => $this->tags ?? [],
        'keywords' => $this->extractKeywords(), // Custom method to extract content keywords
    ];
}

public function shouldBeSearchable(): bool
{
    return $this->status === 'open' && 
           $this->forum?->status === 'open' && 
           !$this->trashed();
}

// Helper method to extract keywords from thread content
private function extractKeywords(): array
{
    $content = $this->title . ' ' . ($this->posts()->oldest()->first()?->content ?? '');
    $content = strip_tags($content);
    
    // Extract common programming/tech terms, product names, etc.
    preg_match_all('/\b(?:zen-?cart|plugin|module|payment|shipping|admin|customer|order|product|category|tax|email|template|override|function|error|bug|fix|install|upgrade|version|php|mysql|sql|css|html|javascript|jquery)\b/i', $content, $matches);
    
    return array_unique(array_map('strtolower', $matches[0] ?? []));
}
```

### Post Model Implementation (Enhanced)
```php
// app/Models/Post.php
public function toSearchableArray(): array
{
    return [
        // Basic post information
        'id' => $this->id,
        'content' => strip_tags($this->content),
        'content_length' => strlen(strip_tags($this->content)),
        'excerpt' => Str::limit(strip_tags($this->content), 200),
        'status' => $this->status,
        
        // User context
        'user_id' => $this->user_id,
        'user_name' => $this->user?->name,
        'user_reputation' => $this->calculateUserReputation(),
        'user_total_posts' => $this->user?->posts()->count() ?? 0,
        'user_total_likes_received' => $this->user?->posts()->withCount('likes')->get()->sum('likes_count') ?? 0,
        
        // Thread context
        'thread_id' => $this->thread_id,
        'thread_title' => $this->thread?->title,
        'thread_view_count' => $this->thread?->views ?? 0,
        'thread_post_count' => $this->thread?->posts()->count() ?? 0,
        'thread_is_pinned' => $this->thread?->pinned ?? false,
        'position_in_thread' => $this->calculatePositionInThread(),
        'is_first_post' => $this->isFirstPost(),
        
        // Forum hierarchy
        'forum_id' => $this->forum_id,
        'forum_name' => $this->forum?->name,
        'forum_slug' => $this->forum?->slug,
        'forum_group_id' => $this->forum?->group?->id,
        'forum_group_name' => $this->forum?->group?->name,
        'forum_group_slug' => $this->forum?->group?->slug,
        
        // Engagement metrics
        'like_count' => $this->likes()->count(),
        'read_count' => $this->readBy()->count(),
        'engagement_score' => $this->calculateEngagementScore(),
        
        // Content analysis
        'has_code_blocks' => $this->hasCodeBlocks(),
        'has_links' => $this->hasExternalLinks(),
        'has_images' => $this->hasImages(),
        'mentioned_users' => $this->extractMentionedUsers(),
        'content_keywords' => $this->extractContentKeywords(),
        
        // Temporal data
        'created_at' => $this->created_at?->timestamp,
        'updated_at' => $this->updated_at?->timestamp,
        'is_edited' => $this->created_at != $this->updated_at,
        'days_since_created' => $this->created_at?->diffInDays(now()) ?? 0,
        'hour_of_day' => $this->created_at?->hour ?? 0,
        'day_of_week' => $this->created_at?->dayOfWeek ?? 0,
        
        // Quality indicators
        'helpfulness_score' => $this->calculateHelpfulnessScore(),
        'spam_risk_score' => $this->calculateSpamRisk(),
    ];
}

public function shouldBeSearchable(): bool
{
    return $this->status === 'open' && 
           $this->thread?->status === 'open' && 
           $this->forum?->status === 'open' &&
           !$this->trashed() &&
           $this->calculateSpamRisk() < 0.5; // Don't index likely spam
}

// Helper methods for enhanced post analysis
private function calculateUserReputation(): float
{
    $user = $this->user;
    if (!$user) return 0;
    
    $totalPosts = $user->posts()->count();
    $totalLikes = $user->posts()->withCount('likes')->get()->sum('likes_count');
    
    // Simple reputation calculation
    return ($totalLikes * 2) + ($totalPosts * 0.1);
}

private function calculatePositionInThread(): int
{
    return $this->thread?->posts()
        ->where('created_at', '<=', $this->created_at)
        ->count() ?? 1;
}

private function isFirstPost(): bool
{
    return $this->thread?->posts()->oldest()->first()?->id === $this->id;
}

private function calculateEngagementScore(): float
{
    $likes = $this->likes()->count();
    $reads = $this->readBy()->count();
    $age_days = $this->created_at?->diffInDays(now()) ?? 1;
    
    // Engagement score that factors in age decay
    return ($likes * 3 + $reads * 1) / max($age_days * 0.1, 1);
}

private function hasCodeBlocks(): bool
{
    return preg_match('/<code>|<pre>|```/', $this->content) === 1;
}

private function hasExternalLinks(): bool
{
    return preg_match('/https?:\/\//', $this->content) === 1;
}

private function hasImages(): bool
{
    return preg_match('/<img|!\[.*\]\(/', $this->content) === 1;
}

private function extractMentionedUsers(): array
{
    preg_match_all('/@(\w+)/', $this->content, $matches);
    return array_unique($matches[1] ?? []);
}

private function extractContentKeywords(): array
{
    $content = strip_tags($this->content);
    
    // Extract Zen Cart specific terms and common tech keywords
    preg_match_all('/\b(?:zen-?cart|plugin|module|addon|payment|shipping|admin|customer|order|product|category|tax|email|template|override|function|error|bug|fix|install|upgrade|version|php|mysql|sql|css|html|javascript|jquery|bootstrap|responsive|mobile|seo|ssl|https|database|backup|security|performance|optimization|cache|session|cookie|debug|log|configuration|setting|attribute|option|discount|coupon|zone|country|language|currency|layout|sidebar|header|footer|navigation|menu|search|filter|sort|pagination|checkout|cart|wishlist|compare|review|rating|comment|notification|subscription|newsletter|social|facebook|twitter|google|paypal|stripe|authorize|square)\b/i', $content, $matches);
    
    return array_unique(array_map('strtolower', $matches[0] ?? []));
}

private function calculateHelpfulnessScore(): float
{
    $score = 0;
    
    // Base score from likes
    $score += $this->likes()->count() * 2;
        
    // Bonus for detailed posts
    $wordCount = str_word_count(strip_tags($this->content));
    if ($wordCount > 50) $score += 2;
    if ($wordCount > 200) $score += 3;
    
    // Bonus for code examples
    if ($this->hasCodeBlocks()) $score += 3;
    
    // Bonus for helpful links
    if ($this->hasExternalLinks()) $score += 1;
    
    return $score;
}

private function calculateSpamRisk(): float
{
    $risk = 0;
    
    $content = strip_tags($this->content);
    $wordCount = str_word_count($content);
    
    // Very short posts
    if ($wordCount < 10) $risk += 0.3;
    
    // Too many links
    $linkCount = substr_count($content, 'http');
    if ($linkCount > 3) $risk += 0.4;
    
    // Repeated characters
    if (preg_match('/(.)\1{4,}/', $content)) $risk += 0.3;
    
    // All caps
    if ($content === strtoupper($content) && strlen($content) > 20) $risk += 0.2;
    
    return min($risk, 1.0);
}
```

### Forum Model Implementation (Enhanced)
```php
// app/Models/Forum.php
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
        'avg_posts_per_thread' => $this->thread_count > 0 ? round($this->post_count / $this->thread_count, 2) : 0,
        'avg_thread_views' => $this->thread_count > 0 ? round($this->total_thread_views / $this->thread_count, 2) : 0,
        'acceptance_rate' => $this->thread_count > 0 ? round(($this->solved_thread_count / $this->thread_count) * 100, 2) : 0,
        
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
```

### ForumGroup Model Implementation (Enhanced)
```php
// app/Models/ForumGroup.php
use Laravel\Scout\Searchable;

// Add to class: use Searchable;

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
```

### PluginVersion Model Implementation
```php
// app/Models/PluginVersion.php
public function toSearchableArray(): array
{
    return [
        'id' => $this->id,
        'plugin_id' => $this->plugin_id,
        'plugin_name' => $this->plugin?->name,
        'plugin_slug' => $this->plugin?->slug,
        'plugin_description' => $this->plugin?->description,
        'plugin_group_id' => $this->plugin?->plugin_group_id,
        'plugin_group_name' => $this->plugin?->group?->name,
        'plugin_tags' => $this->plugin?->tags ?? [],
        'version' => $this->version,
        'description' => $this->description,
        'php_version' => $this->php_version,
        'status' => $this->status,
        'is_encapsulated' => $this->is_encapsulated,
        'user_id' => $this->user_id,
        'user_name' => $this->user?->name,
        'compatible_zencart_versions' => $this->zencartVersions->pluck('version')->toArray(),
        'zencart_versions_count' => $this->zencartVersions->count(),
        'file_size' => $this->file_size,
        'has_file' => $this->hasFile(),
        'download_count' => $this->count ?? 0,
        'created_at' => $this->created_at?->timestamp,
        'updated_at' => $this->updated_at?->timestamp,
    ];
}

public function shouldBeSearchable(): bool
{
    return $this->plugin?->status === 'open' && $this->status === 'approved';
}
```

### PluginGroup Model Implementation
```php
// app/Models/PluginGroup.php
use Laravel\Scout\Searchable;

// Add to class: use Searchable;

public function toSearchableArray(): array
{
    return [
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
```

### PluginStatistic Model Implementation (Optional)
```php
// app/Models/PluginStatistic.php
use Laravel\Scout\Searchable;

// Add to class: use Searchable;

public function toSearchableArray(): array
{
    return [
        'id' => $this->id,
        'plugin_id' => $this->plugin_id,
        'plugin_name' => $this->plugin?->name,
        'plugin_group_name' => $this->plugin?->group?->name,
        'user_id' => $this->user_id,
        'user_name' => $this->user?->name,
        'action' => $this->action, // 'view' or 'download'
        'ip_address' => $this->ip_address,
        'user_agent' => $this->user_agent,
        'date' => $this->created_at?->format('Y-m-d'),
        'month' => $this->created_at?->format('Y-m'),
        'year' => $this->created_at?->format('Y'),
        'created_at' => $this->created_at?->timestamp,
    ];
}

public function shouldBeSearchable(): bool
{
    // Only index recent statistics (last 6 months) to keep index manageable
    return $this->created_at > now()->subMonths(6);
}
```

### ZencartVersion Model Implementation (Optional)
```php
// app/Models/ZencartVersion.php
use Laravel\Scout\Searchable;

// Add to class: use Searchable;

public function toSearchableArray(): array
{
    return [
        'id' => $this->id,
        'version' => $this->version,
        'compatible_plugins_count' => $this->pluginVersions()->count(),
        'compatible_plugins' => $this->pluginVersions()
            ->with('plugin')
            ->whereHas('plugin', fn($q) => $q->where('status', 'open'))
            ->get()
            ->pluck('plugin.name')
            ->unique()
            ->values()
            ->toArray(),
        'latest_plugin_versions' => $this->pluginVersions()
            ->with('plugin')
            ->whereHas('plugin', fn($q) => $q->where('status', 'open'))
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(fn($pv) => [
                'plugin_name' => $pv->plugin->name,
                'version' => $pv->version,
                'created_at' => $pv->created_at->timestamp,
            ])
            ->toArray(),
        'created_at' => $this->created_at?->timestamp,
        'updated_at' => $this->updated_at?->timestamp,
    ];
}
```

## 7. Search Features by Model

### Plugin Search Capabilities
- Full-text search on name, description, group name, and tags
- Filter by featured status
- Filter by plugin group
- Filter by status (though only 'open' are indexed)
- Sort by downloads, views, or date
- Popularity-based ranking

### Enhanced Plugin-Related Search Capabilities

#### PluginVersion Search
- **Use Cases**:
  - Find specific plugin versions by version number
  - Search by compatibility with Zen Cart versions
  - Find plugins by PHP version requirements
  - Locate encapsulated vs non-encapsulated plugins
- **Features**:
  - Full plugin context (name, description, group, tags)
  - Zen Cart compatibility filtering
  - File availability filtering
  - Version-specific descriptions

#### PluginGroup Search
- **Use Cases**:
  - Discover popular plugin categories
  - Find groups with most active development
  - Browse by total download/view metrics
- **Features**:
  - Aggregate statistics across all plugins in group
  - Popular plugin names for context
  - Activity-based ranking

#### PluginStatistic Search (Analytics)
- **Use Cases**:
  - Track download/view patterns
  - Analyze user engagement trends
  - Generate usage reports
- **Features**:
  - Time-based filtering (date, month, year)
  - Action-based filtering (views vs downloads)
  - User activity tracking
  - Recent data only (6 months) for performance

#### ZencartVersion Search
- **Use Cases**:
  - Find plugins compatible with specific Zen Cart versions
  - Discover most supported Zen Cart versions
  - Track plugin ecosystem evolution
- **Features**:
  - Compatible plugin discovery
  - Latest plugin versions for each Zen Cart version
  - Compatibility statistics

### Enhanced Forum Search Capabilities

#### Thread Search
- **Primary Use Cases**:
  - Find solutions to specific problems
  - Discover popular discussions on topics
  - Locate threads by author or forum
  - Search for recently active discussions
- **Advanced Features**:
  - Content keyword extraction and matching
  - User reputation-based ranking
  - Engagement metrics (views, replies, likes, subscriptions)
  - Thread status filtering (pinned, solved, open)
  - Activity recency scoring
  - Forum hierarchy context

#### Post Search
- **Primary Use Cases**:
  - Find specific answers or solutions
  - Locate posts with code examples
  - Search within thread conversations
  - Discover helpful user contributions
- **Advanced Features**:
  - Content analysis (code blocks, links, images)
  - Helpfulness scoring based on likes and acceptance
  - User reputation weighting
  - Thread context inclusion
  - Spam filtering
  - Position-aware ranking (first posts vs replies)

#### Forum Discovery Search
- **Primary Use Cases**:
  - Find active communities
  - Discover forums by topic or expertise level
  - Locate high-quality discussion areas
  - Browse by activity trends
- **Advanced Features**:
  - Activity trend analysis (growing vs declining)
  - Quality metrics (acceptance rates, engagement)
  - User participation statistics
  - Popular topic extraction
  - Expertise level indicators

#### ForumGroup Search
- **Primary Use Cases**:
  - Discover major topic categories
  - Find the most active discussion areas
  - Browse by community engagement levels
  - Locate specialized communities
- **Advanced Features**:
  - Aggregate statistics across all forums
  - Dominant topic identification
  - Community health metrics
  - Cross-forum trend analysis
  - Engagement scoring

### Search Ranking Strategies

#### Thread Ranking Priority
1. **Pinned Status** - Administrative priority
2. **Solved Status** - Threads with accepted answers
3. **Total Likes** - Community appreciation
4. **Subscriber Count** - User interest level
5. **View Count** - Popular content
6. **Recent Activity** - Fresh discussions

#### Post Ranking Priority
1. **Accepted Answer** - Verified solutions
2. **Helpfulness Score** - Detailed, code-rich, well-liked posts
3. **Engagement Score** - Age-adjusted popularity
4. **User Reputation** - Trusted contributors
5. **Like Count** - Community validation

#### Content Quality Indicators
- **Thread Quality**: View count, subscriber count, solution status
- **Post Quality**: Like count, acceptance status, content length, code examples
- **User Authority**: Post count, likes received, accepted answers
- **Freshness**: Recent activity, new content
- **Engagement**: Community interaction levels

## 8. Implementation Notes

### Performance Considerations
1. Plugin search prioritizes popular content via download/view count ranking
2. Chunk size of 500 prevents memory issues during bulk operations
3. Typo tolerance improves user experience for imperfect queries

### Content Moderation
- Only "open" plugins are searchable, providing automatic content filtering
- Other models don't have searchability conditions

### Missing Features
1. Forum search lacks custom configuration
2. No search highlighting configuration
3. No synonym support configured
4. No stop words configured
5. No faceting configuration for advanced filtering

## 9. Environment Variables

Required environment variables for search:
```env
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://localhost:7700
MEILISEARCH_KEY=your-master-key
```

Optional configuration:
```env
SCOUT_PREFIX=
SCOUT_QUEUE=false
```

## 10. Improving Search Data & Metadata

### Current Schema Limitations

#### Thread Model
Currently indexes only default attributes. Missing:
- User information (author name, reputation)
- Forum hierarchy (forum name, group name)
- Engagement metrics (reply count, view count, last activity)
- Content quality indicators (pinned status, solved status)

#### Post Model
Currently indexes only default attributes. Missing:
- Thread context (thread title, forum name)
- User information (author name, reputation)
- Content metadata (word count, language)
- Engagement metrics (likes, helpful votes)

#### Forum Model
Basic implementation missing:
- Activity metrics (last post date, post count)
- Hierarchical context (group name)
- Popular topics/tags

### Recommended Search Improvements

#### 1. Enhanced Thread Search Implementation
```php
public function toSearchableArray(): array
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'slug' => $this->slug,
        // User context
        'user_id' => $this->user_id,
        'user_name' => $this->user->name,
        'user_reputation' => $this->user->reputation ?? 0,
        // Forum context
        'forum_id' => $this->forum_id,
        'forum_name' => $this->forum->name,
        'forum_group_name' => $this->forum->group->name,
        // Engagement metrics
        'view_count' => $this->views,
        'post_count' => $this->posts_count,
        'unique_participants' => $this->participants()->count(),
        'last_activity_at' => $this->last_post_at ?? $this->created_at,
        // Content metadata
        'is_pinned' => $this->pinned,
        'is_locked' => $this->isLocked(),
        'is_solved' => $this->is_solved ?? false,
        'tags' => $this->tags ?? [],
        // Dates
        'created_at' => $this->created_at->timestamp,
        'updated_at' => $this->updated_at->timestamp,
    ];
}
```

#### 2. Enhanced Post Search Implementation
```php
public function toSearchableArray(): array
{
    return [
        'id' => $this->id,
        'content' => $this->content,
        'excerpt' => Str::limit(strip_tags($this->content), 200),
        // Thread context
        'thread_id' => $this->thread_id,
        'thread_title' => $this->thread->title,
        'forum_id' => $this->forum_id,
        'forum_name' => $this->forum->name,
        // User context
        'user_id' => $this->user_id,
        'user_name' => $this->user->name,
        'user_post_count' => $this->user->posts_count,
        // Engagement metrics
        'like_count' => $this->likes_count ?? 0,
        'is_accepted_answer' => $this->is_accepted_answer ?? false,
        // Content metadata
        'word_count' => str_word_count(strip_tags($this->content)),
        'has_code_blocks' => str_contains($this->content, '<code>'),
        'mentioned_users' => $this->extractMentions(),
        // Dates
        'created_at' => $this->created_at->timestamp,
        'edited_at' => $this->updated_at != $this->created_at ? $this->updated_at->timestamp : null,
    ];
}
```

#### 3. Enhanced Forum Search Implementation
```php
public function toSearchableArray(): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'slug' => $this->slug,
        'description' => $this->description,
        // Hierarchy
        'group_id' => $this->forum_group_id,
        'group_name' => $this->group->name,
        // Activity metrics
        'thread_count' => $this->threads_count,
        'post_count' => $this->posts_count,
        'last_post_at' => $this->last_post_at,
        'active_users_count' => $this->activeUsers()->count(),
        // Popular topics (could be computed)
        'popular_tags' => $this->getPopularTags(),
        'trending_topics' => $this->getTrendingTopics(),
        // Metadata
        'is_private' => $this->is_private ?? false,
        'required_reputation' => $this->required_reputation ?? 0,
        'created_at' => $this->created_at->timestamp,
    ];
}
```

### Meilisearch Configuration Improvements

#### Enhanced Forum Index Configurations

##### Thread Index Configuration
```php
'threads' => [
    'searchableAttributes' => [
        'title', 'first_post_excerpt', 'keywords', 'tags',
        'user_name', 'forum_name', 'forum_group_name'
    ],
    'filterableAttributes' => [
        'forum_id', 'forum_group_id', 'user_id', 'status',
        'is_pinned', 'has_accepted_answer', 'created_at', 'latest_post_at',
        'view_count', 'post_count', 'subscriber_count', 'days_since_activity'
    ],
    'sortableAttributes' => [
        'view_count', 'post_count', 'subscriber_count', 'unique_participants',
        'total_likes', 'latest_post_at', 'created_at', 'days_since_activity'
    ],
    'rankingRules' => [
        'words', 'typo', 'proximity', 'attribute', 'sort', 'exactness',
        'is_pinned:desc',           // Pinned threads always rank higher
        'has_accepted_answer:desc', // Solved threads rank higher
        'total_likes:desc',         // Well-liked threads rank higher
        'subscriber_count:desc',    // Popular threads rank higher
        'view_count:desc',          // Viewed threads rank higher
        'latest_post_at:desc',      // Recent activity ranks higher
    ],
    'distinctAttribute' => 'id',
    'faceting' => [
        'maxValuesPerFacet' => 100,
    ],
]
```

##### Post Index Configuration
```php
'posts' => [
    'searchableAttributes' => [
        'content', 'excerpt', 'content_keywords', 'mentioned_users',
        'thread_title', 'user_name', 'forum_name', 'forum_group_name'
    ],
    'filterableAttributes' => [
        'thread_id', 'forum_id', 'forum_group_id', 'user_id', 'status',
        'is_accepted_answer', 'is_first_post', 'has_code_blocks', 'has_links',
        'has_images', 'created_at', 'like_count', 'read_count', 'position_in_thread',
        'content_length', 'user_reputation', 'helpfulness_score', 'engagement_score'
    ],
    'sortableAttributes' => [
        'like_count', 'read_count', 'engagement_score', 'helpfulness_score',
        'user_reputation', 'content_length', 'created_at', 'position_in_thread'
    ],
    'rankingRules' => [
        'words', 'typo', 'proximity', 'attribute', 'sort', 'exactness',
        'is_accepted_answer:desc',  // Solutions always rank highest
        'helpfulness_score:desc',   // Helpful posts rank higher
        'engagement_score:desc',    // Engaging posts rank higher
        'user_reputation:desc',     // Posts from reputable users rank higher
        'like_count:desc',          // Liked posts rank higher
    ],
    'distinctAttribute' => 'id',
    'faceting' => [
        'maxValuesPerFacet' => 100,
    ],
]
```

##### Forum Index Configuration
```php
'forums' => [
    'searchableAttributes' => [
        'name', 'description', 'group_name', 'group_description',
        'popular_thread_titles', 'common_keywords'
    ],
    'filterableAttributes' => [
        'forum_group_id', 'status', 'thread_count', 'post_count',
        'open_thread_count', 'solved_thread_count', 'pinned_thread_count',
        'unique_thread_authors', 'unique_post_authors', 'active_users_last_30_days',
        'days_since_last_post', 'avg_posts_per_thread', 'acceptance_rate',
        'recent_activity_trend', 'created_at'
    ],
    'sortableAttributes' => [
        'thread_count', 'post_count', 'total_thread_views', 'total_post_likes',
        'unique_thread_authors', 'active_users_last_30_days', 'acceptance_rate',
        'avg_posts_per_thread', 'recent_activity_trend', 'latest_post_at'
    ],
    'rankingRules' => [
        'words', 'typo', 'proximity', 'attribute', 'sort', 'exactness',
        'recent_activity_trend:desc',    // Growing forums rank higher
        'active_users_last_30_days:desc', // Active forums rank higher
        'acceptance_rate:desc',          // Helpful forums rank higher
        'total_post_likes:desc',         // Well-regarded forums rank higher
        'post_count:desc',               // Active forums rank higher
    ],
    'distinctAttribute' => 'id',
]
```

##### ForumGroup Index Configuration
```php
'forum_groups' => [
    'searchableAttributes' => [
        'name', 'description', 'popular_forum_names',
        'dominant_topics', 'common_keywords', 'most_active_forum'
    ],
    'filterableAttributes' => [
        'forum_count', 'active_forum_count', 'total_threads', 'total_posts',
        'open_threads', 'solved_threads', 'unique_thread_authors',
        'unique_post_authors', 'active_users_last_30_days', 'active_users_last_7_days',
        'acceptance_rate', 'activity_trend', 'days_since_last_activity', 'created_at'
    ],
    'sortableAttributes' => [
        'total_threads', 'total_posts', 'total_views', 'total_likes',
        'engagement_score', 'acceptance_rate', 'activity_trend',
        'active_users_last_30_days', 'latest_post_at'
    ],
    'rankingRules' => [
        'words', 'typo', 'proximity', 'attribute', 'sort', 'exactness',
        'activity_trend:desc',           // Growing groups rank higher
        'engagement_score:desc',         // High engagement groups rank higher
        'active_users_last_30_days:desc', // Active groups rank higher
        'acceptance_rate:desc',          // Helpful groups rank higher
        'total_posts:desc',              // Large groups rank higher
    ],
    'distinctAttribute' => 'id',
]
```

#### Plugin-Related Index Configurations
```php
'plugin_versions' => [
    'searchableAttributes' => [
        'plugin_name', 'plugin_description', 'version', 'description', 
        'plugin_group_name', 'plugin_tags', 'compatible_zencart_versions'
    ],
    'filterableAttributes' => [
        'plugin_id', 'plugin_group_id', 'status', 'is_encapsulated',
        'user_id', 'php_version', 'has_file', 'created_at'
    ],
    'sortableAttributes' => [
        'download_count', 'file_size', 'zencart_versions_count', 
        'created_at', 'updated_at'
    ],
    'rankingRules' => [
        'words', 'typo', 'proximity', 'attribute', 'sort', 'exactness',
        'download_count:desc',
        'zencart_versions_count:desc', // More compatible versions rank higher
    ],
],
'plugin_groups' => [
    'searchableAttributes' => ['name', 'description', 'popular_plugin_names'],
    'filterableAttributes' => [
        'plugins_count', 'active_plugins_count', 'featured_plugins_count',
        'latest_plugin_date'
    ],
    'sortableAttributes' => [
        'plugins_count', 'active_plugins_count', 'total_downloads', 
        'total_views', 'latest_plugin_date'
    ],
    'rankingRules' => [
        'words', 'typo', 'proximity', 'attribute', 'sort', 'exactness',
        'active_plugins_count:desc',
        'total_downloads:desc',
    ],
],
'plugin_statistics' => [
    'searchableAttributes' => ['plugin_name', 'plugin_group_name', 'user_name'],
    'filterableAttributes' => [
        'plugin_id', 'user_id', 'action', 'date', 'month', 'year', 'created_at'
    ],
    'sortableAttributes' => ['created_at'],
    'rankingRules' => [
        'words', 'typo', 'proximity', 'attribute', 'sort', 'exactness',
        'created_at:desc',
    ],
],
'zencart_versions' => [
    'searchableAttributes' => ['version', 'compatible_plugins'],
    'filterableAttributes' => ['compatible_plugins_count'],
    'sortableAttributes' => ['compatible_plugins_count', 'created_at'],
    'rankingRules' => [
        'words', 'typo', 'proximity', 'attribute', 'sort', 'exactness',
        'compatible_plugins_count:desc',
    ],
],
```

### Database Schema Enhancements Needed

#### 1. Add to Threads Table
```sql
ALTER TABLE threads ADD COLUMN last_post_at TIMESTAMP NULL;
ALTER TABLE threads ADD COLUMN post_count INTEGER DEFAULT 0;
ALTER TABLE threads ADD COLUMN participant_count INTEGER DEFAULT 0;
ALTER TABLE threads ADD COLUMN is_solved BOOLEAN DEFAULT FALSE;
ALTER TABLE threads ADD COLUMN tags JSON NULL;
```

#### 2. Add to Posts Table
```sql
ALTER TABLE posts ADD COLUMN is_accepted_answer BOOLEAN DEFAULT FALSE;
ALTER TABLE posts ADD COLUMN like_count INTEGER DEFAULT 0;
ALTER TABLE posts ADD COLUMN word_count INTEGER DEFAULT 0;
```

#### 3. Add to Forums Table
```sql
ALTER TABLE forums ADD COLUMN thread_count INTEGER DEFAULT 0;
ALTER TABLE forums ADD COLUMN post_count INTEGER DEFAULT 0;
ALTER TABLE forums ADD COLUMN last_post_at TIMESTAMP NULL;
```

### Search Experience Enhancements

#### 1. Faceted Search Implementation
Enable filtering by:
- Forum/Forum Group
- Date ranges (Last 24h, Week, Month, Year)
- Thread status (Solved, Pinned, Locked)
- User (My posts, Specific user)
- Engagement level (Hot, Active, Unanswered)

#### 2. Search Result Relevance Boosting
- Boost recent content (last 30 days)
- Boost threads with accepted answers
- Boost content from high-reputation users
- Boost posts with high engagement (likes, replies)

#### 3. Advanced Search Features
- Search within specific forums
- Exclude certain forums/users
- Search by post type (questions, answers, discussions)
- Date range filtering
- Minimum engagement thresholds

## 11. Future Considerations

### Potential Improvements
1. Add custom search configuration for forum models
2. Implement search highlighting
3. Add synonym support for common terms
4. Configure stop words for better relevance
5. Add faceted search for advanced filtering
6. Implement search analytics
7. Add search suggestions/autocomplete
8. Implement "similar threads" functionality
9. Add search result snippets with context

### Search UI Components (Removed)
The following components were removed and need reimplementation:
- Forum full-page search (`/forums/search`)
- Forum inline search dropdown
- Plugin search page (`/plugins/search`)
- Search routes and navigation links

---

**Note**: This document should be updated whenever changes are made to the search implementation or configuration.