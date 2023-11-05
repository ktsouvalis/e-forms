<?php

namespace App\Models\microapps;

use App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $table="tickets";
    protected $guarded = [
        'id'
    ];

    public function school(){
        return $this->belongsTo(School::class);
    }

    public function posts(){
        return $this->hasMany(TicketPost::class);
    }
}
