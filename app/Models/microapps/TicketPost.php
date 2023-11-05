<?php

namespace App\Models\microapps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPost extends Model
{
    use HasFactory;

    protected $table='ticket_posts';

    protected $fillable = [
        'ticket_id',
        'text',
        'ticketer_id',
        'ticketer_type',
    ];

    public function ticketer()
    {
        return $this->morphTo();
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
