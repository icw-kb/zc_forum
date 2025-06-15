<?php

namespace App\Policies;

use App\Models\Plugin;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PluginPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_plugin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Plugin $plugin): bool
    {
        // Public viewing - anyone can view plugins
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_plugin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Plugin $plugin): bool
    {
        return $user->can('update_plugin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Plugin $plugin): bool
    {
        return $user->can('delete_plugin');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_plugin');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Plugin $plugin): bool
    {
        return $user->can('force_delete_plugin');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_plugin');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Plugin $plugin): bool
    {
        return $user->can('restore_plugin');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_plugin');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Plugin $plugin): bool
    {
        return $user->can('replicate_plugin');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_plugin');
    }

    /**
     * Determine whether the user can download the plugin.
     * Downloads require authentication but no special permissions.
     */
    public function download(User $user, Plugin $plugin): bool
    {
        // Must be authenticated to download
        return $user !== null;
    }

    /**
     * Determine whether anyone (including guests) can view the plugin listing.
     */
    public function viewListing(?User $user): bool
    {
        // Public access to plugin listings
        return true;
    }

    /**
     * Determine whether anyone (including guests) can search plugins.
     */
    public function search(?User $user): bool
    {
        // Public access to plugin search
        return true;
    }
}
