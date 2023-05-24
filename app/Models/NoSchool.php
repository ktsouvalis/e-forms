<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoSchool extends Model
{
    use HasFactory;

    protected $table='no_schools';

    protected $fillable = [
        'name'
    ];

    public function teachers()
    {
        return $this->morphMany(Teacher::class, 'ypiretisi');
    }
}
