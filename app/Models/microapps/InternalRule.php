<?php

namespace App\Models\microapps;

use App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InternalRule extends Model
{
    use HasFactory;

    protected $table = 'internal_rules';
    protected $guarded = ['id'];

    public $timestamps = false;
    protected $fillable = [
        'school_id',
        'school_file',
        'school_file2',
        'school_file3',
        'consultant_comments_file',
        'director_comments_file',
        'approved_by_consultant',
        'approved_by_director',
        'consultant_signed_file',
        'director_signed_file',
        'school_updated_at',
        'consultant_approved_at',
        'director_approved_at',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
