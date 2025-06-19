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
}
