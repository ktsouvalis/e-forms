<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    //
    public function test(Teacher $teacher){
        return view('test',['teacher'=>$teacher]);
    }
}
