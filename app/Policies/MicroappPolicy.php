<?php

namespace App\Policies;

use App\Models\Microapp;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MicroappPolicy
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
    public function view(User $user, Microapp $microapp): bool
    {
        //
        return $user->microapps->where('microapp_id', $microapp->id)->count();
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
    public function update(User $user, Microapp $microapp): bool
    {
        //
        // if($user->microapps->where('microapp_id', $microapp->id)->first()->can_edit){
        //     return true;
        // }
        // return false;

        return ($microapp->active and $user->microapps->where('microapp_id', $microapp->id)->first()->can_edit);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Microapp $microapp): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Microapp $microapp): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Microapp $microapp): bool
    {
        //
    }

    public function deactivate(User $user): bool {
        return in_array($user->id,[1,2]);
    }

    public function beViewedByAdmins(User $user, Microapp $microapp): bool{
        if($microapp->active) return true;
        //if(in_array($user->id,[1,2])) return true;
        return false;
            
    }
}
