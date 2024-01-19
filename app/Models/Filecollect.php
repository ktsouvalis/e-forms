<?php

namespace App\Models;

use App\Models\FilecollectUser;
use App\Models\FilecollectStakeholder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Filecollect extends Model
{
    use HasFactory;

    protected $table="filecollects";

    protected $guarded =[
        'id'
    ];

    public function users(){
        return $this->hasMany(FilecollectUser::class);
    }

    public function stakeholders(){
        return $this->hasMany(FilecollectStakeholder::class);
    }
}
