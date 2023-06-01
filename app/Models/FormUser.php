<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormUser extends Model
{
    use HasFactory;

    protected $table ='forms_users';
    protected $guarded=[
        'id'
    ];

    public function form(){
        return $this->belongsTo(Form::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
