<?php

namespace App\Models\microapps;

use App\Models\microapps\Timetables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimetablesFiles extends Model
{
    use HasFactory;
    protected $table = 'timetables_files';
    protected $fillable = [
        'timetable_id',
        'filenames_json',
        'timestamps_json',
        'comments',
        'status',
    ];

    public function timetable(){
        return $this->belongsTo(Timetables::class, 'timetable_id', 'id');
    }
}
