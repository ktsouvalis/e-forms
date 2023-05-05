<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersMenus extends Model
{
    use HasFactory;

    protected $table = 'users_menus';

    protected $fillable = [
        'user_id',
        'menu_id'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function menu(){
        return $this->belongsTo(Menu::class,'menu_id');
    }
}
