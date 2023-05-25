<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    use HasFactory;

    protected $table='directories';

    protected $guarded = [
        'id'
    ];
    
    protected $fillable = [
        'code',
        'name',
        'mail',
        'telephone'
    ];

    public function teachers()
    {
        return $this->morphMany(Teacher::class, 'organiki');
    }
}
