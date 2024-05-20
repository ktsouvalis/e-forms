<?php

namespace App\Models;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkExperience extends Model
{
    use HasFactory;

    protected $table = 'work_experiences';
    
    protected $guarded = [
        'id'
    ];

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }

}
