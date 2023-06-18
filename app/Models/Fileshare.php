<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fileshare extends Model
{
    use HasFactory;

    protected $table="fileshares";

    protected $guarded =[
        'id'
    ];

    public function department(){
        return $this->belongsTo(Department::class);
    }

    public function stakeholders(){
        return $this->hasMany(FileshareStakeholder::class);
    }
}
