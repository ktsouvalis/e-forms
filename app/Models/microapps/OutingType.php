<?php

namespace App\Models\microapps;

use App\Models\microapps\Outing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutingType extends Model
{
    use HasFactory;

    protected $table = 'outingtypes';
    protected $guarded = ['id'];

    public function outings(){
        return $this->hasMany(Outing::class);
    }
}
