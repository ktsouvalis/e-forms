<?php

namespace App\Models\microapps;

use App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BuildingProblems extends Model
{
    use HasFactory;

    protected $table = 'building_problems';
    protected $guarded = [
        'id'
    ];

    public function school(){
        return $this->belongsTo(School::class);
    }
}
