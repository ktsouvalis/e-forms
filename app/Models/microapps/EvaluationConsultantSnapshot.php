<?php

namespace App\Models\microapps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationConsultantSnapshot extends Model
{
    use HasFactory;
     protected $guarded = ['id'];

    protected $casts = [
        'data' => 'array',
    ];
}
