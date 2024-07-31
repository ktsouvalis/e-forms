<?php

namespace App\Models\microapps;

use App\Models\School;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeacherLeaves extends Model
{
    use HasFactory;

    protected $table = 'teacher_leaves';
    protected $guarded = ['id'];

    public function teacher() {
        return $this->belongsTo(Teacher::class, 'afm', 'afm');
    }

    public function school() {
        return $this->belongsTo(School::class, 'creator_entity_code', 'code');
    }
}
