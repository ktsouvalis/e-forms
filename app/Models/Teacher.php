<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Authenticatable
{
    use HasFactory;

    protected $guard = "teacher";
    
    protected $guarded = [
        'id'
    ];

    public function organiki(){
        return $this->morphTo('organiki', 'organiki_type', 'organiki_id');
    }

    public function ypiretisi(){
        return $this->morphTo('ypiretisi', 'ypiretisi_type', 'ypiretisi_id');
    }

    public function sxesi_ergasias(){
        return $this->belongsTo(SxesiErgasias::class,'sxesi_ergasias_id');
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
}