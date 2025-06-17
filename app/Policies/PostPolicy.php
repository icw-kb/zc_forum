<?php

namespace App\Policies;

use App\Models\Forum;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Allow everyone to view posts
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Post $post): bool
    {
        // For now, allow all users including guests to view posts
        // TODO: Implement proper restriction checking once the system is fully configured
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Forum $forum): bool
    {
        // TEMPORARY: Permissions disabled for testing
        // Must be logged in to create posts
        if (! $user) {
            return false;
        }

        return true;

        // // Check if user has permission to create posts
        // if (! $user->can('create_post')) {
        //     return false;
        // }

        // // For now, logged-in users with create_post permission can post in any forum
        // // TODO: Implement proper forum-level restriction checking
        // return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // // Check if user owns the post
        // if ($post->user_id === $user->id) {
        //     // Check if within edit time limit (e.g., 15 minutes)
        //     $editTimeLimit = now()->subMinutes(15);
        //     if ($post->created_at->gt($editTimeLimit)) {
        //         return $user->can('edit_post');
        //     }
        // }

        // // Check if user can edit any post
        // return $user->can('edit_any_post');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // // Check if user owns the post
        // if ($post->user_id === $user->id) {
        //     return $user->can('delete_post');
        // }

        // // Check if user can delete any post
        // return $user->can('delete_any_post');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // return $user->can('restore_post');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // return $user->can('force_delete_post');
    }
}
