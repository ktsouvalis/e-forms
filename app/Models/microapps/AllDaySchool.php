<?php

namespace App\Models\microapps;

use App\Models\Month;
use App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AllDaySchool extends Model
{
    use HasFactory;

    protected $table="all_day_school";
    protected $guarded = [
        'id'
    ];
    //belongs to meaning one record of this model has one school
    public function school(){
        return $this->belongsTo(School::class);
    }

    public function month(){
        return $this->belongsTo(Month::class);
    }
}
