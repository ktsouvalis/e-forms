<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassName extends Model
{
    use HasFactory;
    protected $table = 'class_names';
    protected $guarded = [
        'id'
    ];

    public $timestamps = false;

}
