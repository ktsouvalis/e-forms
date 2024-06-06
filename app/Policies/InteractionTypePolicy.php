<?php

namespace App\Policies;

use App\Models\InteractionType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InteractionTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InteractionType $InteractionType): bool
    {
        //
        if($user->isAdmin())
            return true;
        return $user->department->id === $InteractionType->department_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InteractionType $InteractionType): bool
    {
        //
        return $this->view($user, $InteractionType);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InteractionType $InteractionType): bool
    {
        //
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create_type_on_behalf(User $user)
    {
        return $user->isAdmin();
    }
}
