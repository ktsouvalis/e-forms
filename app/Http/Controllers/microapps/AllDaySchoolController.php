<?php

namespace App\Http\Controllers\microapps;

use App\Models\Month;
use App\Models\School;
use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\microapps\AllDaySchool;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class AllDaySchoolController extends Controller
{
    //
    public function post_all_day(Request $request){
        $school = Auth::guard('school')->user();
        $microapp = Microapp::where('url', '/all_day_school')->first();
        if($microapp->accepts){
            if(isset($request->all()['nr_class_3']))
                $noc3 = $request->all()['nr_class_3'];
            else
                $noc3=0;
            $noc4 = $request->all()['nr_class_4'];
            $noc5 = $request->all()['nr_class_5'];
            if(isset($request->all()['nr_pupils_3']))
                $nos3 = $request->all()['nr_pupils_3'];
            else
                $nos3=0;
            $nos4 = $request->all()['nr_pupils_4'];
            $nos5 = $request->all()['nr_pupils_5'];
            $comments= $request->all()['comments'];
            $functionality = $request->all()['functionality'];
            $month = Month::getActiveMonth();
            if(!$school->vmonth or $school->vmonth->vmonth == 0){
                $month_to_store = $month->id;
            }
            else{
                $month_to_store = $school->vmonth->vmonth;
            }
            if($request->file('table_file')){
                $rule = [
                    'table_file' => 'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ];
                $validator = Validator::make($request->all(), $rule);
                if($validator->fails()){ 
                    return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου (Επιτρεπτός τύπος: xlsx)');
                }
                $file = $request->file('table_file')->getClientOriginalName();
                //store the file
                $filename = "all_day_".$school->code."_".$month_to_store.".xlsx";
                try{
                    $path = $request->file('table_file')->storeAs('all_day', $filename);
                }
                catch(Throwable $e){
                    try{
                        Log::channel('stakeholders_microapps')->error(Auth::guard('school')->user()->name." create all_day file error ".$e->getMessage());
                    }
                    catch(Throwable $e){
            
                    }
                    return redirect(url('/school_app/all_day_school'))->with('failure', 'Δεν έγινε η αποθήκευση του αρχείου, προσπαθήστε ξανά');     
                }

                try{
                    AllDaySchool::updateOrCreate(
                    [
                        'month_id'=>$month_to_store,
                        'school_id'=>$school->id
                    ],
                    [
                        'functionality'=> $functionality,
                        'comments' => $comments,
                        'nr_of_pupils_3' => $nos3,
                        'nr_of_class_3' => $noc3, 
                        'nr_of_pupils_4' => $nos4,
                        'nr_of_class_4' =>  $noc4,
                        'nr_of_pupils_5' => $nos5,
                        'nr_of_class_5' => $noc5,
                        'nr_morning' => $nosm,
                        'file' => $file
                    ]); 
                }
                catch(Throwable $e){
                    try{
                        Log::channel('stakeholders_microapps')->error(Auth::guard('school')->user()->name." create all_day db error ".$e->getMessage());
                    }
                    catch(Throwable $e){
            
                    }
                    return redirect(url('/school_app/all_day_school'))->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
                }
            }
            else{
                try{    
                    AllDaySchool::updateOrCreate(
                    [
                        'month_id'=>$month_to_store,
                        'school_id'=>$school->id
                    ],
                    [
                        'functionality'=> $functionality,
                        'comments'=> $comments,
                        'nr_of_class_3' => $noc3,
                        'nr_of_pupils_3' => $nos3,
                        'nr_of_class_4' =>  $noc4,
                        'nr_of_pupils_4' => $nos4,
                        'nr_of_class_5' => $noc5,
                        'nr_of_pupils_5' => $nos5,
                    ]); 
                }
                catch(Throwable $e){
                    try{
                        Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create all_day db error without file '.$e->getMessage());
                    }
                    catch(Throwable $e){
            
                    }
                    return redirect(url('/school_app/all_day_school'))->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
                }  
            }
            $show = Month::where('number', $month_to_store)->first()->name;
            try{
                Log::channel('stakeholders_microapps')->info(Auth::guard('school')->user()->name." create/update all_day success $show");
            }
            catch(Throwable $e){
    
            }
            return redirect(url('/school_app/all_day_school'))->with('success', "Τα στοιχεία για τον μήνα $show ενημερώθηκαν");
        }
        else{
            return redirect(url('/school_app/all_day_school'))->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
        }
    }

    public function download_file(Request $request, AllDaySchool $all_day_school){
        $all_day_school_id = Microapp::where('url', '/all_day_school')->first()->id;
        if((Auth::check() && (Auth::user()->microapps->where('microapp_id', $all_day_school_id)->count() or Auth::user()->isAdmin())) || (Auth::guard('school')->check() && Auth::guard('school')->user()->id == $all_day_school->school->id)){    
            $file = 'all_day/all_day_'.$all_day_school->school->code.'_'.$all_day_school->month->id.'.xlsx';
            $response = Storage::disk('local')->download($file, $all_day_school->file);  
            ob_end_clean();
            try{
                return $response;
            }
            catch(Throwable $e){
                return back()->with('failure', 'Δεν ήταν δυνατή η λήψη του αρχείου, προσπαθήστε ξανά');    
            }
        }
        abort(403, 'Unauthorized action.');
    }

    public function update_all_day_template(Request $request, $type){
        $rule = [
            'template_file' => 'required|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου (Επιτρεπτός τύπος: xlsx)');
        }
        
        try{
            $path = $request->file('template_file')->storeAs('all_day', "oloimero_$type.xlsx");  
        }
        catch(Throwable $e){
            return back()->with('failure', 'Δεν έγινε η αποθήκευση του αρχείου, προσπαθήστε ξανά');     
        }
        
        return back()->with('success', 'Το αρχείο ενημερώθηκε');
    }

    public function self_update(Request $request, AllDaySchool $all_day_school){
        if(isset($request->all()['nos3']))
            $all_day_school->update(['nr_of_pupils_3'=>$request->all()['nos3']]);
        $all_day_school->update(['nr_of_pupils_4'=>$request->all()['nos4']]);
        $all_day_school->update(['nr_of_pupils_5'=>$request->all()['nos5']]);

        return back()->with('success', 'Τα στοιχεία ενημερώθηκαν');
    }
}