<?php

namespace App\Models\microapps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPlan extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];

    public function consultant()
    {
        $this->belongsTo(Consultant::class);
    }
}
