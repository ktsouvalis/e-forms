<?php

namespace App\Models\microapps;

use App\Models\School;
use App\Models\microapps\ActionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Action extends Model
{
    use HasFactory;

    protected $table = 'actions';
    protected $guarded = ['id'];

    public function school(){
        return $this->belongsTo(School::class);
    }

    public function type(){
        return $this->belongsTo(ActionType::class, 'actiontype_id');
    }

    public function outings(){
        return $this->hasMany(Outing::class);
    }
}
