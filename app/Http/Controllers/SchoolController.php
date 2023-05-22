<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolController extends Controller
{
    //
    public function login($md5){ 
        $msg = "Δε βρέθηκε η σελίδα που ζητήσατε";
        $state="failure";
        $school = School::where('md5', $md5)->first();
        if($school){
            auth()->guard('school')->logout();
            Auth::guard('school')->login($school);
            session()->regenerate();
            $msg=$school->name." καλωσήρθατε";
            $state = 'success';
        }
        return redirect('/school')->with($state,$msg);
    }

    public function logout(){
        
        auth()->guard('school')->logout();
        return redirect('/school')->with('success', 'Αποσυνδεθήκατε');
    }
}
