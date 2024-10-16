<?php

namespace App\Models;


// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\microapps\Desks;
use App\Models\microapps\Fruit;
use App\Models\microapps\Outing;
use App\Models\microapps\Ticket;
use App\Models\microapps\TwoFile;
use App\Models\microapps\Immigrant;
use App\Models\microapps\Enrollment;
use App\Models\microapps\SchoolArea;
use App\Models\microapps\Timetables;
use App\Models\microapps\AllDaySchool;
use App\Models\microapps\InternalRule;
use App\Models\microapps\Defibrillator;
use App\Models\microapps\TeacherLeaves;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class School extends Authenticatable
{
    use HasFactory;

    protected $guard = "school";

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

    public function organikis()
    {
        return $this->morphMany(Teacher::class, 'organiki');
    }

    public function ypiretisis()
    {
        return $this->morphMany(Teacher::class, 'ypiretisi');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }

    public function director()
    {
        return $this->belongsTo(Teacher::class, 'director_id');
    }

    public function schregion()
    {
        return $this->belongsTo(Schregion::class, 'schregion_id');
    }

    public function forms(){
        return $this->morphMany(FormStakeholder::class, 'stakeholder');
    }

    public function microapps(){
        return $this->morphMany(MicroappStakeholder::class, 'stakeholder');
    }

    public function filecollects(){
        return $this->morphMany(FilecollectStakeholder::class, 'stakeholder');
    }

    public function fileshares(){
        return $this->morphMany(FileshareStakeholder::class, 'stakeholder');
    }

    public function enrollments(){
        return $this->hasOne(Enrollment::class);
    }

    public function fruit(){
        return $this->hasOne(Fruit::class);
    }

    public function twoFile()
    {
        return $this->hasOne(TwoFile::class);
    }

    public function desks(){
        return $this->hasOne(Desks::class);
    }

    public function school_area(){
        return $this->hasOne(SchoolArea::class);
    }

    public function vmonth(){
        return $this->hasOne(VirtualMonth::class);
    }

    public function tickets(){
        return $this->hasMany(Ticket::class);
    }

    public function posts(){
        return $this->morphMany(TicketPost::class, 'ticketer');
    }

    public function all_day_schools(){
        return $this->hasMany(AllDaySchool::class);
    }

    public function immigrants(){
        return $this->hasMany(Immigrant::class);
    }

    public function defibrillators(){
        return $this->hasOne(Defibrillator::class);
    }

    public function sections(){
        return $this->hasMany(Section::class)->orderBy('sec_code', 'asc');
    }

    public function outings(){
        return $this->hasMany(Outing::class)->orderBy('outing_date', 'desc');
    }

    public function internal_rule(){
        return $this->hasOne(InternalRule::class);
    }

    public function leaves(){
        return $this->hasMany(TeacherLeaves::class, 'creator_entity_code', 'code')->orderBy('creation_date', 'desc');
    }

    public function timetables(){
        return $this->hasMany(Timetables::class);
    }

    public function revokedLeaves() {
        return $this->hasMany(TeacherLeaves::class, 'creator_entity_code', 'code')
                    ->withoutGlobalScope('leave_state')
                    ->where('leave_state', '5-Ανακλήθηκε');
    }

    public function addedbys()
    {
        return $this->morphMany(FileshareStakeholder::class, 'addedby');
    }
        /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        //'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
