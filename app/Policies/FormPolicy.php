<?php

namespace App\Policies;

use App\Models\Form;
use App\Models\User;
use App\Models\FormUser;
use App\Models\Superadmin;
use App\Models\FormStakeholder;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class FormPolicy
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
    public function view(Form $form): bool
    {
        //
        if(Superadmin::where('user_id',$user->id)->exists()) return true;
        if(FormUser::where('user_id',Auth::id())
            ->where('form_id', $form->id)
            ->exists()){
                return true;
        }

        if(FormStakeholder::where('stakeholder_id',Auth::guard('teacher')->id())
            ->where('stakeholder_type', 'App\Models\Teacher')
            ->where('form_id', $form->id)
            ->exists()){
                return true;
        }

        if(FormStakeholder::where('stakeholder',Auth::guard('school')->id())
            ->where('form_id', $form->id)
            ->where('stakeholder_type', 'App\Models\School')
            ->exists()){
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
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Form $form): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Form $form): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Form $form): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Form $form): bool
    {
        //
    }
}
