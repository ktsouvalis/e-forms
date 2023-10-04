<?php

namespace App\Models\microapps;

use App\Models\Section;
use App\Models\microapps\Outing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutingSection extends Model
{
    use HasFactory;

    protected $table = 'outings_sections';
    protected $guarded = ['id'];

    public function outing(){
        return $this->belongsTo(Outing::class);
    }

    public function section(){
        return $this->belongsTo(Section::class);
    }
}
