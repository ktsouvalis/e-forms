<?php

namespace App\Policies;

use App\Models\Fileshare;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FilesharePolicy
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
    public function view(User $user, Fileshare $fileshare): bool
    {
        //
        return $user->fileshares->where('fileshare_id', $fileshare->id)->count();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return in_array($user->id,[1,2]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Fileshare $fileshare): bool
    {
        //
        // if($user->Fileshares->where('fileshare_id', $fileshare->id)->first()->can_edit){
        //     return true;
        // }
        // return false;

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Fileshare $fileshare): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Fileshare $fileshare): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Fileshare $fileshare): bool
    {
        //
    }

    public function deactivate(User $user): bool {
        return in_array($user->id,[1,2]);
    }

    public function beViewedByAdmins(User $user, Fileshare $fileshare): bool{
        if($Fileshare->active) return true;
        //if(in_array($user->id,[1,2])) return true;
        return false;
            
    }
}
