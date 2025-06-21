<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;

class Post extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use Auditable, HasFactory, Searchable, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_accepted_answer' => 'boolean',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    /**
     * Get the likes for the post.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    /**
     * Get the users who liked this post.
     */
    public function likedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes')
            ->withTimestamps();
    }

    /**
     * Check if a user has liked this post.
     */
    public function isLikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->likedBy()->where('user_id', $user->id)->exists();
    }

    /**
     * Like this post by a user.
     */
    public function like(User $user): void
    {
        $this->likedBy()->syncWithoutDetaching($user->id);
    }

    /**
     * Unlike this post by a user.
     */
    public function unlike(User $user): void
    {
        $this->likedBy()->detach($user->id);
    }

    /**
     * Get the total number of likes for this post.
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    /**
     * Get the read records for the post.
     */
    public function reads(): HasMany
    {
        return $this->hasMany(PostRead::class);
    }

    /**
     * Get the users who have read this post.
     */
    public function readBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    /**
     * Check if a user has read this post.
     */
    public function isReadBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->readBy()->where('user_id', $user->id)->exists();
    }

    /**
     * Mark this post as read by a user.
     */
    public function markAsRead(User $user): void
    {
        $this->readBy()->syncWithoutDetaching([
            $user->id => ['read_at' => now()],
        ]);
    }

    /**
     * Check if this post is new (unread) for a user.
     */
    public function isNewFor(?User $user): bool
    {
        return ! $this->isReadBy($user);
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            // Basic post information
            'id' => $this->id,
            'content' => strip_tags($this->content),
            'content_length' => strlen(strip_tags($this->content)),
            'excerpt' => \Str::limit(strip_tags($this->content), 200),
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
            'is_accepted_answer' => $this->is_accepted_answer ?? false,
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

    /**
     * Determine if the model should be searchable.
     */
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
        $acceptedAnswers = $user->posts()->where('is_accepted_answer', true)->count();
        
        // Simple reputation calculation
        return ($totalLikes * 2) + ($acceptedAnswers * 5) + ($totalPosts * 0.1);
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
        
        // Bonus for accepted answer
        if ($this->is_accepted_answer) {
            $score += 10;
        }
        
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
}
