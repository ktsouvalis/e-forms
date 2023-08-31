<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Month extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'active',
        'number'
    ];

    public static function getActiveMonth(){
        return self::where('active', 1)->firstOrFail();
    }

    public function all_day_schools(){
        return $this->hasMany(AllDaySchool::class);
    }
}
