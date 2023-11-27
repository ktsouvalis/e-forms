<?php

namespace App\Http\Controllers\microapps;

use Illuminate\Http\Request;
use App\Models\microapps\WorkPlan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WorkPlanController extends Controller
{
    //
    public function saveWorkPlan($yearWeek, Request $request){

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
}
