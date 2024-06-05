<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    use HasFactory;

    protected $table='interactions';
    protected $guarded=['id'];

    public function stakeholder(){
        return $this->morphTo();
    }

    public function interactionType(){
        return $this->belongsTo(InteractionType::class);
    }
}
