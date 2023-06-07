<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicroappUser extends Model
{
    use HasFactory;

    protected $table ='microapps_users';

    protected $guarded=[
        'id'
    ];

    public function microapp(){
        return $this->belongsTo(Microapp::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
