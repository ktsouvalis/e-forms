<?php

namespace App\Policies;

use App\Models\User;
use App\Models\School;
use App\Models\Operation;
use App\Models\Superadmin;
use Illuminate\Auth\Access\Response;

class SchoolPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        if(Superadmin::where('user_id',$user->id)->exists()) return true;
        $operation = Operation::find(1); // schools operation is id 1 from the seeder
        if($operation->users->where('user_id', $user->id)->count()) return true;
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, School $school): bool
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
    public function update(User $user, School $school): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, School $school): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, School $school): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, School $school): bool
    {
        //
    }

    public function upload(User $user): bool
    {
        if(Superadmin::where('user_id',$user->id)->exists()) return true;
        foreach($user->operations->where('can_edit', 1) as $one_operation){
            if($one_operation->operation->url=='/schools'){
                return true;
            }  
        }
        return false;  
    }
}
