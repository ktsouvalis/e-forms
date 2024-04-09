<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessCriteria extends Model
{
    use HasFactory;

    protected $table = 'access_criteria';
    protected $fillable = ['app_type', 'app_id', 'criteria'];
    
    public function app(){
        return $this->morphTo('app_type', 'app_id');
    }  
}
