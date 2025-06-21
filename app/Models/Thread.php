<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;

class Thread extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use Auditable, HasFactory, Searchable, Sluggable, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'pinned' => 'boolean',
        'tags' => 'array',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function latestPost()
    {
        return $this->hasOne(Post::class)->latest();
    }

    /**
     * Get the subscriptions for the thread.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * Get the users who are subscribed to this thread.
     */
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'thread_subscriptions')
            ->withPivot('email_notifications')
            ->withTimestamps();
    }

    /**
     * Check if a user is subscribed to this thread.
     */
    public function isSubscribedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->subscribers()->where('user_id', $user->id)->exists();
    }

    /**
     * Subscribe a user to this thread.
     */
    public function subscribe(User $user, bool $emailNotifications = true): void
    {
        $this->subscribers()->syncWithoutDetaching([
            $user->id => ['email_notifications' => $emailNotifications],
        ]);
    }

    /**
     * Unsubscribe a user from this thread.
     */
    public function unsubscribe(User $user): void
    {
        $this->subscribers()->detach($user->id);
    }

    /**
     * Check if a thread has unread posts for a user.
     */
    public function hasUnreadPostsFor(?User $user): bool
    {
        if (! $user) {
            return true; // Guests see all posts as unread
        }

        return $this->posts()
            ->whereDoesntHave('readBy', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->exists();
    }

    /**
     * Mark all posts in this thread as read for a user.
     */
    public function markAllAsRead(User $user): void
    {
        $posts = $this->posts()->get();

        foreach ($posts as $post) {
            $post->markAsRead($user);
        }
    }

    /**
     * Get the last read post for a user in this thread.
     */
    public function getLastReadPostFor(?User $user)
    {
        if (! $user) {
            return null;
        }

        return $this->posts()
            ->whereHas('readBy', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->first();
    }

    /**
     * Check if the thread is pinned.
     */
    public function isPinned(): bool
    {
        return $this->pinned;
    }

    /**
     * Check if the thread is locked.
     */
    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }

    /**
     * Check if the thread is closed.
     */
    public function isClosed(): bool
    {
        return in_array($this->status, ['closed', 'locked']);
    }

    /**
     * Pin the thread.
     */
    public function pin(): void
    {
        $this->update(['pinned' => true]);
    }

    /**
     * Unpin the thread.
     */
    public function unpin(): void
    {
        $this->update(['pinned' => false]);
    }

    /**
     * Lock the thread.
     */
    public function lock(): void
    {
        $this->update(['status' => 'locked']);
    }

    /**
     * Unlock the thread.
     */
    public function unlock(): void
    {
        $this->update(['status' => 'open']);
    }

    /**
     * Close the thread.
     */
    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }

    /**
     * Open the thread.
     */
    public function open(): void
    {
        $this->update(['status' => 'open']);
    }

    /**
     * Get the indexable data array for the model.
     */
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
            'has_accepted_answer' => $this->posts()->where('is_accepted_answer', true)->exists(),
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
                \Str::limit(strip_tags($this->posts()->oldest()->first()->content), 200) : '',
            
            // Popular tags/keywords (if implemented)
            'tags' => $this->tags ?? [],
            'keywords' => $this->extractKeywords(),
        ];
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->status === 'open' && 
               $this->forum?->status === 'open' && 
               !$this->trashed();
    }

    /**
     * Helper method to extract keywords from thread content.
     */
    private function extractKeywords(): array
    {
        $content = $this->title . ' ' . ($this->posts()->oldest()->first()?->content ?? '');
        $content = strip_tags($content);
        
        // Extract common programming/tech terms, product names, etc.
        preg_match_all('/\b(?:zen-?cart|plugin|module|payment|shipping|admin|customer|order|product|category|tax|email|template|override|function|error|bug|fix|install|upgrade|version|php|mysql|sql|css|html|javascript|jquery)\b/i', $content, $matches);
        
        return array_unique(array_map('strtolower', $matches[0] ?? []));
    }
}
