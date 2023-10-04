<?php

namespace App\Models;

use App\Models\microapps\OutingSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory;

    protected $table = 'sections';

    protected $guarded = [
        'id'
    ];

    public function school(){
        return $this->belongsTo(School::class);
    }

    public function outings(){
        return $this->hasMany(OutingSection::class);
    }

}
