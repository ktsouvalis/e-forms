<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Operation;
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
        if($user->operations->where('operation_id', $operation->id)->count()){
            return true;
        }
        // $useroperations = UsersOperations::where('user_id', $user->id)->get();
        // foreach($useroperations as $one_operation){
        //     if($one_operation->operation_id == $operation->id){
        //         return true;
        //     }
        // }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return in_array($user->id, [1, 2]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Operation $operation): bool
    {
        //
        // if($operation->id == 2 or $operation->name == 'Εκπαιδευτικοί'){
        //     if($user->id != 1 and $user->id != 2){
        //         return false;
        //     }
        // }
        // return true;
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
}
