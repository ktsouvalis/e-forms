<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Microapp;
use App\Models\Superadmin;
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
        if($user->isAdmin()) return true;
        return $user->microapps->where('microapp_id', $microapp->id)->count();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Microapp $microapp): bool
    {
        if($user->isAdmin()){
            return true;
        }
        if(!$microapp->active){
            return false;
        }
        
        if($user->microapps->where('microapp_id', $microapp->id)){
            if($user->microapps->where('microapp_id', $microapp->id)->first()->can_edit){
                return true;
            }  
        }

        return false;    
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
        return $user->isAdmin();
    }

    public function addUser(User $user): bool {
        return $user->isAdmin();   
    }
}
