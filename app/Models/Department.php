<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $guarded = [
        'id'
    ];

    public function users(){
        return $this->hasMany(User::class);
    }

    public function fileshares(){
        return $this->hasMany(Fileshare::class);
    }

    public function filecollects(){
        return $this->hasMany(Filecollect::class);
    }
}
