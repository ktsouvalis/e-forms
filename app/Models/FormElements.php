<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormElements extends Model
{
    use HasFactory;
    protected $table='form_elements';
    public $timestamps = false;

    protected $fillable = [
        'type',
        'attributes'
    ];

    
}
