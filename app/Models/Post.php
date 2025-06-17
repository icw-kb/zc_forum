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
}
