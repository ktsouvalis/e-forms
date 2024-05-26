<?php

namespace App\Models\microapps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollmentsClasses extends Model
{
    use HasFactory;
    protected $table="enrollments_classes";

    protected $guarded =[
        'id'
    ];

    public function enrollment(){
        return $this->belongsTo(Enrollment::class);
    }
}
