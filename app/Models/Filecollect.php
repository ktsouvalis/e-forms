<?php

namespace App\Models;


use App\Models\Department;
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

    public function department(){
        return $this->belongsTo(Department::class);
    }

    public function stakeholders(){
        return $this->hasMany(FilecollectStakeholder::class);
    }

    public function accessCriteria(){
       return $this->morphOne(AccessCriteria::class, 'app');
    }
}
