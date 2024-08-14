<?php

namespace App\Models\microapps;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Swimming extends Model
{
    use HasFactory;
    protected $table = 'swimming';
    protected $fillable = [
        'teacher_id',
        'mobile_phone',
        'specialty',
        'specialty_files_json',
        'licence',
        'licence_files_json',
        'studied',
        'studied_files_json',
        'coordinator',
        'teacher',
        'comments',
    ];
    public function teacher() {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

}
