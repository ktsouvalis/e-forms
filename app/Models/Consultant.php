<?php

namespace App\Models;

use App\Models\microapps\WorkPlan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Consultant extends Authenticatable
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    /**
     * required method for laravel 11
     *
     * returns the password column of the model
     */
    public function getAuthPasswordName()
    {
        return 'md5';
    }

    public function schregion()
    {
        return $this->hasOne(Schregion::class);
    }

    public function workplans()
    {
        return $this->hasMany(WorkPlan::class);
    }
}
