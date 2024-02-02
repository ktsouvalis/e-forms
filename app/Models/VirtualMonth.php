<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualMonth extends Model
{
    use HasFactory;

    protected $table = 'virtual_months';

    protected $guarded = ['id'];

    public function school(){
        return $this->belongsTo(School::class);
    }
}
