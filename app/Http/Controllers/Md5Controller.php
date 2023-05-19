<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Md5Controller extends Controller
{
    //
    public function login(Request $request){
        // $incomingFields=$request->validate([//the html names of the fields
        //     'md5'=>'required',
        // ]);
        
        if(School::where('md5', $request->md5)->count()){
            $school = School::where('md5', $request->md5)->first();
            Auth::login($school);
            $request->session()->regenerate();
            return redirect('/school')->with('success','Συνδεθήκατε επιτυχώς');
        }else{
            return redirect('/')->with('failure', 'Δεν έχετε δικαίωμα πρόσβασης. Ζητήστε link ή κωδικό.');
        }
    }
}
