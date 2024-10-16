<?php

namespace App\Models\microapps;

use App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fruit extends Model
{
    use HasFactory;

    protected $table="fruits";
    protected $guarded = [
        'id'
    ];

    public function school(){
        return $this->hasOne(School::class);
    }
}
