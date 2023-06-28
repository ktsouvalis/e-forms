<?php

namespace App\Models;


// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\microapps\Fruit;
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
        return $this->belongsTo(School::class, 'municipality_id');
    }

    public function forms(){
        return $this->morphMany(FormStakeholder::class, 'stakeholder');
    }

    public function microapps(){
        return $this->morphMany(MicroappStakeholder::class, 'stakeholder');
    }

    public function all_day(){
        return $this->hasOne(mAllDay::class);
    }

    public function fileshares(){
        return $this->morphMany(FileshareStakeholder::class, 'stakeholder');
    }

    public function fruit(){
        return $this->hasOne(Fruit::class);
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
