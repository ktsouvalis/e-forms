<?php

namespace App\Models\microapps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionType extends Model
{
    use HasFactory;

    protected $table = 'actiontypes';
    protected $guarded = ['id'];
}
