<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    
    protected $guarded=[
        'id'
    ];

    public function users(){
        return $this->hasMany(FormUser::class);
    }

    public function stakeholders(){
        return $this->hasMany(FormStakeholder::class);
    }

}
