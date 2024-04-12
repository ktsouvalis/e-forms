<?php

namespace App\Models\microapps;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Secondment extends Model
{
    use HasFactory;

    protected $table = 'secondments';
    protected $guarded = ['id'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
