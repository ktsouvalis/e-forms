<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Microapp extends Model
{
    use HasFactory;

    protected $table="microapps";
    protected $guarded=[
        'id'
    ];

    public function users(){
        return $this->hasMany(MicroappUser::class);
    }

    public function stakeholders(){
        return $this->hasMany(MicroappStakeholder::class);
    }
}
