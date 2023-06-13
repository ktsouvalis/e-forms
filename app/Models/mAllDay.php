<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mAllDay extends Model
{
    use HasFactory;

    protected $table = 'all_day';

    protected $guarded = [
        'id'
    ];

    public function school(){
        return $this->belongsTo(School::class);
    }
}