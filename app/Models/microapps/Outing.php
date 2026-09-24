<?php

namespace App\Models\microapps;

use App\Models\School;
use App\Models\microapps\Action;
use App\Models\microapps\OutingType;
use App\Models\microapps\OutingSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Outing extends Model
{
    use HasFactory;

    protected $table = 'outings';
    protected $guarded = ['id'];

    public function school(){
        return $this->belongsTo(School::class);
    }

    public function sections(){
        return $this->hasMany(OutingSection::class);
    }

    public function type(){
        return $this->belongsTo(OutingType::class, 'outingtype_id');
    }

    public function actions(){
        return $this->belongsToMany(Action::class, 'action_outing');
    }

    public function getIsLateAttribute()
    {
        if ($this->outingtype_id == 2 || $this->outingtype_id == 3) {
            $daysBefore = 4; // Πολύωρη
        } elseif ($this->outingtype_id == 1) {
            $daysBefore = 2; // Ολιγόωρη
        } else {
            return false; // 
        }

        $deadline = \Illuminate\Support\Carbon::parse($this->outing_date)->subDays($daysBefore)->startOfDay();

        return $this->created_at->greaterThan($deadline);
    }
}
