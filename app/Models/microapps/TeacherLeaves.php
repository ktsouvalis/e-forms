<?php

namespace App\Models\microapps;

use App\Models\School;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeacherLeaves extends Model
{
    use HasFactory;

    protected $table = 'teacher_leaves';
    protected $guarded = ['id'];
    protected $casts = [
        'leave_start_date' => 'date',
        'leave_protocol_date' => 'date',
        'creation_date' => 'date',
        'approved_protocol_date' => 'date',
        'last_change_date' => 'date',
        'protocol_date' => 'date',
    ];
    // Global Scope to fetch only not ανακλήθηκε leaves. It causes many problems so i removed.
    // protected static function booted() {
    //     static::addGlobalScope('leave_state', function (Builder $builder) {
    //         $builder->where('leave_state', '!=', '5-Ανακλήθηκε');
    //     });
    // }

    public static function getRevokedLeaves()
    {
        return static::where('leave_state', '5-Ανακλήθηκε');
    }

    public function relatedLeave()
    {
        return $this->belongsTo(TeacherLeave::class, 'related_leave_id');
    }

    public function linkedLeaves()
    {
        return $this->hasMany(TeacherLeave::class, 'related_leave_id');
    }

    public function teacher() {
        return $this->belongsTo(Teacher::class, 'afm', 'afm');
    }

    public function school() {
        return $this->belongsTo(School::class, 'creator_entity_code', 'code');
    }
}
