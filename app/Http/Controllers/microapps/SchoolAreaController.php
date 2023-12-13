<?php

namespace App\Http\Controllers\microapps;

use Throwable;
use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\microapps\SchoolArea;
use Illuminate\Support\Facades\Auth;

class SchoolAreaController extends Controller
{
    //
    public function save_school_area(Request $request){
        // dd($request->all());
        $school = Auth::guard('school')->user();
        $microapp = Microapp::where('url', '/school_area')->first();
        if($microapp->accepts){
            $done=0;
            $data_array=array();
            $count=0;
            foreach($request->all() as $key=>$value){
                if(strpos($key, 'street')!==false){
                    $count++;
                }
            }
            for($i=1;$i<=$count;$i++){
                $temp_array=[];
                $temp_array['street']= $request->input('street'.$i);
                $temp_array['comment']= $request->input('comment'.$i);
                array_push($data_array,$temp_array);
            }
            
            $data = json_encode($data_array, JSON_UNESCAPED_UNICODE);
            try{
                SchoolArea::updateOrCreate(
                    [
                        'school_id'=>$school->id
                    ],
                    [
                        'data' => $data,
                        'comments' => $request->input('general_com')
                    ]
                );
                $done=1;
            }
            catch(Throwable $e){
                Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create school area db error '.$e->getMessage());
                return redirect(url('/school_app/school_area'))->with('failure', 'Η εγγραφή δεν αποθηκεύτηκε. Προσπαθήστε ξανά');
            }

            if($done){
                $stakeholder = $microapp->stakeholders->where('stakeholder_id', $school->id)->where('stakeholder_type', 'App\Models\School')->first();
                $stakeholder->hasAnswer = 1;
                $stakeholder->save();
            }
            return redirect(url('/school_app/school_area'))->with('success', 'Η εγγραφή αποθηκεύτηκε.');
        }
        else{
            return redirect(url('/school_app/school_area'))->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
        }
    }

}
