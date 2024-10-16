<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Fileshare;
use App\Models\Superadmin;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

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
        if($user->isAdmin()) return true;
        if($user->department->fileshares->find($fileshare->id)) return true;
        return false;
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
    public function update(User $user, Fileshare $fileshare): bool
    {
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

    public function chooseDepartment(User $user): bool{
        return Superadmin::where('user_id',$user->id)->exists();
    }
}
