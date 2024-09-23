<?php

namespace App\Models\microapps;

use App\Models\School;
use Illuminate\Database\Eloquent\Model;
use App\Models\microapps\TimetablesFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Timetables extends Model
{
    use HasFactory;
    protected $table = 'timetables';
    protected $fillable = [
        'school_id',
        'comments',
        'status',
    ];

    public function files(){
        return $this->hasMany(TimetablesFiles::class, 'timetable_id', 'id');
    }

    public function school(){
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
}
