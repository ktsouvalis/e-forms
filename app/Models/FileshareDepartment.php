<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileshareDepartment extends Model
{
    use HasFactory;
    protected $table = 'fileshares_departments';

    protected $guarded = [
        'id'
    ];

    public function fileshare(){
        return $this->belongsTo(Fileshare::class);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }
}
