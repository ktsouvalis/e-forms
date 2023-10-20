<?php

namespace App\Models;


// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\microapps\Fruit;
use App\Models\microapps\Outing;
use App\Models\microapps\Ticket;
use App\Models\microapps\Immigrant;
use App\Models\microapps\AllDaySchool;
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

    public function forms(){
        return $this->morphMany(FormStakeholder::class, 'stakeholder');
    }

    public function microapps(){
        return $this->morphMany(MicroappStakeholder::class, 'stakeholder');
    }

    public function fileshares(){
        return $this->morphMany(FileshareStakeholder::class, 'stakeholder');
    }

    public function fruit(){
        return $this->hasOne(Fruit::class);
    }

    public function tickets(){
        return $this->hasMany(Ticket::class);
    }

    public function all_day_schools(){
        return $this->hasMany(AllDaySchool::class);
    }

    public function immigrants(){
        return $this->hasMany(Immigrant::class);
    }

    public function sections(){
        return $this->hasMany(Section::class);
    }

    public function outings(){
        return $this->hasMany(Outing::class);
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
