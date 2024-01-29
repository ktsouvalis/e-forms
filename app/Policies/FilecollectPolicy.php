<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Superadmin;
use App\Models\Filecollect;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class FilecollectPolicy
{
    /**
     * Create a new policy instance.
     */
    public function view(User $user, Filecollect $filecollect): bool
    {
        //
        if(Superadmin::where('user_id',$user->id)->exists()) return true;
        if($user->filecollects->where('filecollect_id', $filecollect->id)->first()) return true;
        return false;
    }

    public function create(User $user): bool
    {
        //
        return Superadmin::where('user_id',$user->id)->exists();
    }

    public function update(User $user, Filecollect $filecollect): bool
    {
        if(Superadmin::where('user_id',$user->id)->exists()){
            return true;
        }
        else{
            if(!$user->filecollects->where('filecollect_id', $filecollect->id)->isEmpty()){
                if($user->filecollects->where('filecollect_id', $filecollect->id)->first()->can_edit){
                    return true;
                }  
                else{
                    return false;
                } 
            }
            else{
                return false;
            }
        }
    }
}
