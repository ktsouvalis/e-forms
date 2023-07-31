<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Operation;
use App\Models\Superadmin;
use App\Models\UsersOperations;
use Illuminate\Auth\Access\Response;

class OperationPolicy
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
    public function view(User $user, Operation $operation): bool
    {
        //
        if(Superadmin::where('user_id',$user->id)->exists()) return true;
        if($user->operations->where('operation_id', $operation->id)->count()){
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return Superadmin::where('user_id',$user->id)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Operation $operation): bool
    {
        
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Operation $operation): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Operation $operation): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Operation $operation): bool
    {
        //
    }

    public function addUser(User $user): bool {
        return Superadmin::where('user_id',$user->id)->exists();   
    }

    public function changeActiveMonth(User $user){
        if(Superadmin::where('user_id',$user->id)->exists()) return true;
        $operation = Operation::where('url','/month')->first(); 
        return ($operation->users->where('user_id', $user->id)->count());
        return false;
    }

    public function executeCommands(User $user){
        if(Superadmin::where('user_id',$user->id)->exists()) return true;
        return false;   
    }
}
