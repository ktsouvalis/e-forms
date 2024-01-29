<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilecollectStakeholder extends Model
{
    use HasFactory;

    protected $table = 'filecollects_stakeholders';

    protected $guarded = [
        'id'
    ];

    public function filecollect(){
        return $this->belongsTo(Filecollect::class);
    }

    public function stakeholder(){
        return $this->morphTo();
    }

    public function scopeSortedByUser($query){
        return $query->join('filecollects', 'filecollects_stakeholders.filecollect_id', '=', 'filecollects.id')
            ->join('users', 'filecollects.user_id', '=', 'users.id')
            ->orderBy('users.name');
    }
}
