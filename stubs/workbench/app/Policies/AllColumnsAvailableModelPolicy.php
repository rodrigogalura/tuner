<?php

namespace Workbench\App\Policies;

use Workbench\App\Models\AllColumnsAvailableModel;
use Workbench\App\Models\User;

class AllColumnsAvailableModelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AllColumnsAvailableModel $allColumnsAvailableModel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AllColumnsAvailableModel $allColumnsAvailableModel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AllColumnsAvailableModel $allColumnsAvailableModel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AllColumnsAvailableModel $allColumnsAvailableModel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AllColumnsAvailableModel $allColumnsAvailableModel): bool
    {
        return false;
    }
}
