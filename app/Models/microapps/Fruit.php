<?php

namespace App\Models\microapps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
