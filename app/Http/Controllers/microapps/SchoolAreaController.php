<?php

namespace App\Http\Controllers\microapps;

use Throwable;
use Carbon\Carbon;
use App\Models\School;
use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\microapps\SchoolArea;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SchoolController;

class SchoolAreaController extends Controller
{
    //
    public function save_school_area(Request $request, School $school){
        // dd($request->all());
        // $school = Auth::guard('school')->user();
        $microapp = Microapp::where('url', '/school_area')->first();
        if(Auth::guard('school')->check()){
            if($microapp->accepts){
                $school_area = $school->school_area;
                $school_area->confirmed = 1;
                if($request->input('general_com') != "")
                    $school_area->comments = $request->input('general_com');
                try{ 
                    $school_area->save(); 
                }
                catch(\Exception $e){
                    Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' update school area db error '.$e->getMessage());
                    return redirect(url("/school_area_profile/$school->id"))->with('failure', 'Η εγγραφή δεν αποθηκεύτηκε. Προσπαθήστε ξανά');
                }
                $stakeholder = $microapp->stakeholders->where('stakeholder_id', $school->id)->where('stakeholder_type', 'App\Models\School')->first();
                $stakeholder->hasAnswer = 1;
                $stakeholder->save();
                Log::channel('stakeholders_microapps')->info(Auth::guard('school')->user()->name.' confirmed school area '.Carbon::now());
                return redirect(url("/school_area_profile/$school->id"))->with('success', 'Η εγγραφή αποθηκεύτηκε.');  
            }
            else{
                return redirect(url('/school_app/school_area'))->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
            }
        }
        else if(Auth::guard('web')->check()){
            $data_array=array();
            $count=0;
            foreach($request->all() as $key=>$value){
                if(strpos($key, 'street')!==false){
                    $count++;
                }
            }
            for($i=1;$i<=$count;$i++){
                $temp_array=[];
                if($request->input('street'.$i)=="" and $request->input('comment'.$i)==""){
                    continue;
                }
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
                        'comments' => $request->input('general_com'),
                        'confirmed' => 0,
                    ]
                );
            }
            catch(Throwable $e){
                Log::channel('throwable_db')->error(Auth::user()->username.' create school area db error '.$e->getMessage());
                return redirect(url("/school_area_profile/$school->id"))->with('failure', 'Η εγγραφή δεν αποθηκεύτηκε. Προσπαθήστε ξανά');
            }

            Log::channel('stakeholders_microapps')->info(Auth::guard('web')->user()->username.' updated school area '.Carbon::now());
            return redirect(url("/school_area_profile/$school->id"))->with('success', 'Η εγγραφή αποθηκεύτηκε.');
        }
    }
}
