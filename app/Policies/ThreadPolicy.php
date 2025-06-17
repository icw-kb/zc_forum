<?php

namespace App\Policies;

use App\Models\Forum;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThreadPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Allow everyone to view threads list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Thread $thread): bool
    {
        // For now, allow all users including guests to view threads
        // TODO: Implement proper restriction checking once the system is fully configured
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Forum $forum): bool
    {
        // TEMPORARY: Permissions disabled for testing
        // Must be logged in to create threads
        if (! $user) {
            return false;
        }
        return true;

        // // Check if user has permission to create threads
        // if (! $user->can('create_thread')) {
        //     return false;
        // }

        // // For now, logged-in users with create_thread permission can create in any forum
        // // TODO: Implement proper forum-level restriction checking
        // return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Thread $thread): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;
        
        // // Check if user owns the thread
        // if ($thread->user_id === $user->id) {
        //     return $user->can('edit_thread');
        // }

        // // Check if user can edit any thread
        // return $user->can('edit_any_thread');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Thread $thread): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;
        
        // // Check if user owns the thread
        // if ($thread->user_id === $user->id) {
        //     return $user->can('delete_thread');
        // }

        // // Check if user can delete any thread
        // return $user->can('delete_any_thread');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Thread $thread): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;
        
        // return $user->can('restore_thread');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Thread $thread): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;
        
        // return $user->can('force_delete_thread');
    }
}
