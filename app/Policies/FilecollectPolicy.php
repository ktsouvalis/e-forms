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
        if($user->isAdmin()) return true;
        if($user->department->filecollects->find($filecollect->id)) return true;
        return false;
    }

    public function create(User $user): bool
    {
        //
        // return $user->isAdmin();
    }

    public function update(User $user, Filecollect $filecollect): bool
    {
       
    }

    public function chooseDepartment(User $user): bool{
        return $user->isAdmin();
    }
}
