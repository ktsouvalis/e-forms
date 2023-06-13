<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\mAllDay;
use Illuminate\Http\Request;

class AllDayController extends Controller
{
    //
    public function saveData(Request $request, School $school){
        $rec = mAllDay::updateOrCreate(
            [
                'school_id'=>$school->id
            ],
            [
                'school_id'=>$school->id,
                'first_number' =>$request->all()['input1'],
                'second_number' =>$request->all()['input2']
            ]
        );
        // dd($rec); exit;
        
        if(!$rec->wasRecentlyCreated){
            return redirect(url('/school_app/all_day'))->with('success', 'H εγγραφή ανανεώθηκε επιτυχώς');
        }
        return redirect(url('/school_app/all_day'))->with('success', 'H εγγραφή δημιουργήθηκε επιτυχώς');
    }
}
