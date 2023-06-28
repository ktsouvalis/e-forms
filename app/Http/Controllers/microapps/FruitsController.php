<?php

namespace App\Http\Controllers\microapps;

use Throwable;
use App\Models\School;
use App\Models\Microapp;
use Illuminate\Http\Request;
use App\Models\microapps\Fruit;
use App\Http\Controllers\Controller;

class FruitsController extends Controller
{
    //
    public function save_fruits(Request $request, School $school){
        if(Microapp::where('url', '/fruits')->first()->accepts){
            try{
                Fruit::updateOrCreate(
                    [
                        'school_id'=>$school->id
                    ],
                    [
                        'school_id'=>$school->id,
                        'no_of_students' => $request->input('students_number'),
                        'no_of_ukr_students' => $request->input('ukr_students_number'),
                        'comments' => $request->input('comments')
                    ]
                );
            }
            catch(Throwable $exception){
                return redirect(url('/school_app/fruits'))->with('failure', 'Η εγγραφή δεν αποθηκεύτηκε. Προσπαθήστε ξανά');
            }

            return redirect(url('/school_app/fruits'))->with('success', 'Η εγγραφή αποθηκεύτηκε.');
        }
        else{
            return redirect(url('/school_app/fruits'))->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
        }
    }
}
