<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mEndDocument extends Model
{
    use HasFactory;

    protected $table = "end_documents";
    protected $guarded = [
        'id'
    ];

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }
}