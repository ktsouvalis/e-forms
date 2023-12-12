<?php

namespace App\Models\microapps;

use App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolArea extends Model
{
    use HasFactory;
    protected $table='school_areas';

    protected $guarded = [
        'id'
    ];

    public function school() {
        return $this->hasOne(School::class);
    }
}
