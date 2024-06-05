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

    public function interactions()
    {
        return $this->hasManyThrough(
            Interaction::class,
            InteractionType::class,
            'department_id', // Foreign key on InteractionType table...
            'interaction_type_id', // Foreign key on Interactions table...
            'id', // Local key on Departments table...
            'id' // Local key on InteractionTypes table...
        );
    }
}
