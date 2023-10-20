<?php

namespace App\Models\microapps;

use App\Models\Month;
use App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Immigrant extends Model
{
    use HasFactory;

    protected $table="immigrants";
    protected $guarded = [
        'id'
    ];

    public function school(){
        return $this->belongsTo(School::class);
    }

    public function month(){
        return $this->belongsTo(Month::class);
    }
}
