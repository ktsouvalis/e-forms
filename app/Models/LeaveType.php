<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'eProtocolName',
        'eProtocolId'
    ];
    /**
     * Get the leave requests associated with the leave type.
     */
}
