<?php

namespace App\Policies;

use App\Models\Forum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ForumPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // // Allow guests to view forums
        // if (! $user) {
        //     return true;
        // }

        // return $user->can('view_any_forum');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Forum $forum): bool
    {
        // For now, allow all users including guests to view forums
        // TODO: Implement proper restriction checking once the system is fully configured
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // return $user->can('create_forum');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Forum $forum): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // return $user->can('update_forum');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Forum $forum): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // return $user->can('delete_forum');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // return $user->can('delete_any_forum');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Forum $forum): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // return $user->can('force_delete_forum');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // return $user->can('force_delete_any_forum');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Forum $forum): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // return $user->can('restore_forum');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // return $user->can('restore_any_forum');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Forum $forum): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // return $user->can('replicate_forum');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        // TEMPORARY: Permissions disabled for testing
        return true;

        // return $user->can('reorder_forum');
    }
}
