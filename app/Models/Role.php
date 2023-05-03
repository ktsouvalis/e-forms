<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id'
    ];
    
    public function users()
    {
        return $this->hasMany(UsersRoles::class);
    }

    public function role(){
        return $this->belongsTo(Role::class, 'parent_id');
    }

    public function menus(){
        return $this->hasMany(RolesMenus::class);
    }
}
