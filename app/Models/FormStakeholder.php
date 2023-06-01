<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormStakeholder extends Model
{
    use HasFactory;

    protected $table = 'forms_stakeholders';
    protected $guarded = [
        'id'
    ];

    public function form(){
        return $this->belongsTo(Form::class);
    }

    public function stakeholder(){
        return $this->morphTo();
    }
}
