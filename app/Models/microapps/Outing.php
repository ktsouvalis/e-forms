<?php

namespace App\Models\microapps;

use App\Models\School;
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
}
