<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Authenticatable
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function organiki(){
        return $this->morphTo('organiki', 'organiki_type', 'organiki_id');
    }

    public function ypiretisi(){
        return $this->morphTo('ypiretisi', 'ypiretisi_type', 'ypiretisi_id');
    }
}
