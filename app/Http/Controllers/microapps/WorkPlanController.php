<?php

namespace App\Http\Controllers\microapps;

use App\Models\Microapp;
use Illuminate\Http\Request;
use App\Models\microapps\WorkPlan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WorkPlanController extends Controller
{
    //
    public function saveWorkPlan($yearWeek, Request $request){
        $microapp = Microapp::where('url', "/work_planning")->first();
        if($microapp->accepts){
            $programm = json_encode(array(
                    'mon'=> $request->all()['mon'],
                    'tue'=> $request->all()['tue'],
                    'wed'=> $request->all()['wed'],
                    'thu'=> $request->all()['thu'],
                    'fri'=> $request->all()['fri'])
            );
            // dd($programm);
            WorkPlan::updateOrCreate([
                'yearWeek' => $yearWeek,
                'consultant_id' => Auth::guard('consultant')->id()
            ],
            [
                'comments' => $request->all()['comments'],
                'programm' => $programm,
            ]);

            return back()->with('success', 'Ενημερώθηκε το πρόγραμμα');
        }
        else{
            return redirect(url('/consultant_app/work_planning'))->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
        }
    }
}
