<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolUsernameMappings extends Model
{
    use HasFactory;
    protected $table = 'school_username_mappings';
    protected $fillable = ['username', 'school_code'];

    public static function getSchoolCodeByUsername($username)
    {
        return SchoolUsernameMappings::where('username', $username)->value('school_code');
    }

    public static function getUsernameBySchoolCode($schoolCode)
    {
        return SchoolUsernameMappings::where('school_code', $schoolCode)->value('username');
    }
}


