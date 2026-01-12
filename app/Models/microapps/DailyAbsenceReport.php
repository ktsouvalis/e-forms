<?php

namespace App\Models\microapps;

use Carbon\Carbon;
use App\Models\School;
use App\Models\Microapp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyAbsenceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'report_date',
        'absent_count',
        'comments',
        'submitted_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // Check if report can still be edited (before 10 PM deadline)
    public function canBeEdited()
    {
        $deadline = Carbon::parse($this->report_date)->setTime(10, 0, 0);
        return now()->lte($deadline);
    }

    // Check if deadline has passed for a given date
    public static function deadlinePassed($date)
    {
        //  To be removed tomorow - removew also from blade
        $startDate = Carbon::create(2026, 1, 12, 23, 59, 59);
        if (now()->lt($startDate)) {
            return false;
        }
        $deadline = Carbon::parse($date)->setTime(10, 0, 0);
        return now()->gt($deadline);
    }

    // Scope to get reports for a specific date
    public function scopeForDate($query, $date)
    {
        return $query->where('report_date', Carbon::parse($date)->format('Y-m-d'));
    }

    // Scope to get today's reports
    public function scopeToday($query)
    {
        return $query->where('report_date', now()->format('Y-m-d'));
    }

    public static function setMicroappDeadline(){
        $microapp = Microapp::where('url', '/daily_absence_reports')->first();
        if($microapp->closes_at != Carbon::today()->format('Y-m-d'). ' 00:00:00'){
            $microapp->closes_at = Carbon::today()->format('Y-m-d'). ' 00:00:00';
            $microapp->save();
        }
        
    }

}
