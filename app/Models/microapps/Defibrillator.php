<?php

namespace App\Models\microapps;

use App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Defibrillator extends Model
{
    use HasFactory;

    protected $table="defibrillators";
    protected $guarded = [
        'id'
    ];

    public function school(){
        return $this->belongsTo(School::class);
    }
}
