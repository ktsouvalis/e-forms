<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SxesiErgasias extends Model
{
    use HasFactory;

    protected $table='sxesi_ergasias';
    protected $fillable =[
        'monimos',
        'name',
    ];

    public function teachers(){
        return $this->hasMany(Teacher::class);
    }

}
