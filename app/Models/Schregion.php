<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schregion extends Model
{
    use HasFactory;

    protected $table = 'schregions';

    protected $guarded = [
        'id'
    ];

    public function schools()
    {
        return $this->hasMany(School::class);
    }

    public function consultant()
    {
        return $this->belongsTo(Consultant::class);
    }
}
