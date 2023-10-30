<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Operation;
use App\Models\Consultant;
use Illuminate\Auth\Access\Response;

class ConsultantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        // return true;
        if($user->isAdmin()) return true;
        $operation = Operation::where('url', '/consultants')->first();
        return ($operation->users->where('user_id', $user->id)->count());
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Consultant $consultant): bool
    {
        //
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
    public function update(User $user, Consultant $consultant): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Consultant $consultant): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Consultant $consultant): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Consultant $consultant): bool
    {
        //
    }
}
