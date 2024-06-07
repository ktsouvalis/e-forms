<?php

namespace App\Models\microapps;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Secondment extends Model
{
    use HasFactory;

    protected $table = 'secondments';
    protected $guarded = ['id'];
    protected static function booted() {
        static::addGlobalScope('revoked', function (Builder $builder) {
            $builder->where('revoked', 0);
        });
    }

    public static function getRevoked() {
        return self::withoutGlobalScope('revoked')->where('revoked', 1)->get();
    }
    
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
