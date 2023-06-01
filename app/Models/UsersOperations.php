<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersOperations extends Model
{
    use HasFactory;

    protected $table = 'users_operations';

    protected $fillable = [
        'user_id',
        'operation_id',
        'can_edit'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function operation(){
        return $this->belongsTo(Operation::class,'operation_id');
    }
}
