<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicroappStakeholder extends Model
{
    use HasFactory;

    protected $table = 'microapps_stakeholders';

    protected $guarded = [
        'id'
    ];

    public function microapp(){
        return $this->belongsTo(Microapp::class);
    }

    public function stakeholder(){
        return $this->morphTo();
    }
}
