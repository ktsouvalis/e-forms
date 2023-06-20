<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileshareStakeholder extends Model
{
    use HasFactory;
    protected $table = 'fileshares_stakeholders';

    protected $guarded = [
        'id'
    ];

    public function fileshare(){
        return $this->belongsTo(Fileshare::class);
    }

    public function stakeholder(){
        return $this->morphTo();
    }

    public function scopeSortedByDepartment($query){
        return $query->join('fileshares', 'fileshares_stakeholders.fileshare_id', '=', 'fileshares.id')
            ->join('departments', 'fileshares.department_id', '=', 'departments.id')
            ->orderBy('departments.name');
    }
}
