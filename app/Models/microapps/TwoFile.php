<?php

namespace App\Models\microapps;

use App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TwoFile extends Model
{
    use HasFactory;
    protected $table="two_files";
    protected $guarded = [
        'id'
    ];

    public function school(){
        return $this->belongsTo(School::class);
    }
}
