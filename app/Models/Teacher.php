<?php

namespace App\Models;

use App\Models\WorkExperience;
use App\Models\microapps\Secondment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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

    public function secondment(){
        return $this->hasOne(Secondment::class);
    }

    public function work_experience(){
        return $this->hasOne(WorkExperience::class, 'teacher_id');
    }

    public function filecollects(){
        return $this->morphMany(FilecollectStakeholder::class, 'stakeholder');
    }
    
    public function fileshares(){
        return $this->morphMany(FileshareStakeholder::class, 'stakeholder');
    }

    public function addedbys()
    {
        return $this->morphMany(FileshareStakeholder::class, 'addedby');
    }
}