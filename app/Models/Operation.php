<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'url',
        'color',
        'icon',
    ];

    public function users(){
        return $this->hasMany(UsersOperations::class);
    }
}
