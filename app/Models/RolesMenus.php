<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesMenus extends Model
{
    use HasFactory;

    protected $table = 'roles_menus';

    protected $fillable = [
        'role_id',
        'menu_id'
    ];

    public function menu(){
        return $this->belongsTo(Menu::class,'menu_id');
    }

    public function role(){
        return $this->belongsTo(Role::class,'role_id');
    }
}
