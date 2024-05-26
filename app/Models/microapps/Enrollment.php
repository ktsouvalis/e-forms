<?php

namespace App\Models\microapps;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $table="enrollments";

    protected $guarded =[
        'id'
    ];

    public function enrollmentClasses(){
        return $this->hasOne(EnrollmentsClasses::class);
    }
    public function school(){
        return $this->belongsTo(School::class);
    }
}
