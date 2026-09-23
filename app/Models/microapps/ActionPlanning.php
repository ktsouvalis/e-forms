<?php

namespace App\Models\microapps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\School;

class ActionPlanning extends Model
{
    use HasFactory;

    protected $table = 'action_planning';
    protected $guarded = ['id'];

    protected $casts = [
        'files_json' => 'array', // Αυτόματα κάνει json_encode/decode
        'checked' => 'boolean',
    ];
    
    public function school(){
        return $this->belongsTo(School::class);
    }
}
