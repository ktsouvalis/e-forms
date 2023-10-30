<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultantController extends Controller
{
    //
    public function login($md5){ 
       
        $consultant = Consultant::where('md5', $md5)->firstOrFail();
        //logs the teacher in using the 'teacher' guard
        Auth::guard('consultant')->login($consultant);
        session()->regenerate();   
        $consultant->save();

        return redirect(url('/index_consultant'))->with('success',"$consultant->name καλωσήρθατε!");
    }

    public function logout(){
        auth()->guard('consultant')->logout();
        return redirect(url('/index_consultant'))->with('success', 'Αποσυνδεθήκατε');
    }
}
