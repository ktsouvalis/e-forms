<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Operation;
use Illuminate\Auth\Access\Response;

class TeacherPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        $operation = Operation::find(2); // teachers operation is id 2 from the seeder
        return ($operation->users->where('user_id', $user->id)->count());
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Teacher $teacher): bool
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
    public function update(User $user, Teacher $teacher): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Teacher $teacher): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Teacher $teacher): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Teacher $teacher): bool
    {
        //
    }

    public function upload(User $user): bool
    {
        foreach($user->operations->where('can_edit', 1) as $one_operation){
            if($one_operation->operation->url=='/teachers'){
                return true;
            }  
        }
        return false;  
    }
}
