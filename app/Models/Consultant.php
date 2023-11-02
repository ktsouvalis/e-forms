<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultant extends Authenticatable
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function schregion()
    {
        return $this->hasOne(Schregion::class);
    }
}
