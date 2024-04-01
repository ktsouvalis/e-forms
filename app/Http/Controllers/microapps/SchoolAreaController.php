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

class SchoolAreaController extends Controller
{
    //
    private $microapp;

    public function __construct(){
        $this->middleware('auth')->only(['index']);
        $this->middleware('isSchool')->only(['create']);
        $this->middleware('canViewMicroapp')->only(['index', 'create']);
        $this->middleware('canUpdateSchoolArea')->only(['update', 'edit']);
        $this->microapp = Microapp::where('url', '/school_area')->first();
    }

    public function index(){
        return view('microapps.school_area.index', ['appname' => 'school_area']);
    }
    
    public function edit($school_id){
        $school = School::find($school_id);
        return view("microapps.school_area.school_area_profile_admin", ['school'=>$school]);
    }

    public function create(){
        return view('microapps.school_area.school_area_profile_school', ['school' => Auth::guard('school')->user()]);
    }

    public function update(Request $request, $data){
        $school = School::find($data);
        if(Auth::guard('school')->check()){
            if($this->microapp->accepts){
                $school_area = $school->school_area;
                $school_area->confirmed = 1;
                if($request->input('general_com') != "")
                    $school_area->comments = $request->input('general_com');
                try{ 
                    $school_area->save(); 
                }
                catch(\Exception $e){
                    Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' update school area db error '.$e->getMessage());
                    return back()->with('failure', 'Η εγγραφή δεν αποθηκεύτηκε. Προσπαθήστε ξανά');
                }
                $stakeholder = $this->microapp->stakeholders->where('stakeholder_id', $school->id)->where('stakeholder_type', 'App\Models\School')->first();
                $stakeholder->hasAnswer = 1;
                $stakeholder->save();
                Log::channel('stakeholders_microapps')->info(Auth::guard('school')->user()->name.' confirmed school area '.Carbon::now());
                return back()->with('success', 'Η εγγραφή αποθηκεύτηκε.');  
            }
            else{
                return back()->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
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
                return back()->with('failure', 'Η εγγραφή δεν αποθηκεύτηκε. Προσπαθήστε ξανά');
            }

            Log::channel('stakeholders_microapps')->info(Auth::guard('web')->user()->username.' updated school area '.Carbon::now());
            return back()->with('success', 'Η εγγραφή αποθηκεύτηκε.');
        }
    }
}
