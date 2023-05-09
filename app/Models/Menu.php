<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'url',
        'color',
        'icon',
        'active',
        'accepts',
        'viewable'
    ];

    public function users(){
        return $this->hasMany(UsersMenus::class);
    }
}
