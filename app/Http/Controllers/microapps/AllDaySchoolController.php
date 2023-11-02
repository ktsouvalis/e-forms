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
    public function post_all_day(Request $request, School $school){
        $microapp = Microapp::where('url', '/all_day_school')->first();
        if($microapp->accepts){
            if(isset($request->all()['nr_class_3']))
                $noc3 = $request->all()['nr_class_3'];
            else
                $noc3=0;
            $noc4 = $request->all()['nr_class_4'];
            $noc5 = $request->all()['nr_class_5'];
            $comments= $request->all()['comments'];
            $functionality = $request->all()['functionality'];
            $month = Month::getActiveMonth();
            if($request->file('table_file')){
                $rule = [
                    'table_file' => 'mimes:xlsx'
                ];
                $validator = Validator::make($request->all(), $rule);
                if($validator->fails()){ 
                    return redirect()->back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
                }
                $file = $request->file('table_file')->getClientOriginalName();
                //store the file
                $filename = "all_day_".$school->code."_".$month->id.".xlsx";
                try{
                    $path = $request->file('table_file')->storeAs('all_day', $filename);
                }
                catch(Throwable $e){
                    Log::channel('stakeholders_microapps')->error(Auth::guard('school')->user()->name." create all_day file error ".$e->getMessage());
                    return redirect(url('/school_app/all_day_school'))->with('failure', 'Δεν έγινε η αποθήκευση του αρχείου, προσπαθήστε ξανά');     
                }

                //load the file with phpspreadsheet
                $spreadsheet = IOFactory::load("../storage/app/$path");
                
                $row=7;
                $rowSumValue="1";
                $nos3=0;
                $nos4=0;
                $nos5=0;
                $nosm=0;
                while ($rowSumValue != "" && $row<400){
                    $time = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(7, $row)->getValue();
                    if($time=='15:00' or $time=='3:00:00 μμ' or $time==0.625 or $time=='15:00 ή 14:50'){
                        $nos3++;
                    }
                    else if($time=='16:00' or $time=='4:00:00 μμ' or $time==0.6667 or $time=='16:00 ή 15:50'){
                        $nos4++;
                    }
                    else if($time=='17:30' or $time=='5:30:00 μμ' or $time==0.7292){
                        $nos5++;
                    }

                    $morning = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(8, $row)->getValue();
                    if($morning=="ΠΡΩΙΝΗ ΥΠΟΔΟΧΗ"){
                        $nosm++;
                    }
                    $row++;
                    $rowSumValue="";
                    for($col=1;$col<=4;$col++){
                        $rowSumValue .= $spreadsheet->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();   
                    }
                }
                try{
                    AllDaySchool::updateOrCreate(
                    [
                        'month_id'=>$month->id,
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
                    Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create all_day db error '.$e->getMessage());
                    return redirect(url('/school_app/all_day_school'))->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
                }
            }
            else{
                try{    
                    AllDaySchool::updateOrCreate(
                    [
                        'month_id'=>$month->id,
                        'school_id'=>$school->id
                    ],
                    [
                        'functionality'=> $functionality,
                        'comments'=> $comments,
                        'nr_of_class_3' => $noc3,
                        'nr_of_class_4' =>  $noc4,
                        'nr_of_class_5' => $noc5
                    ]); 
                }
                catch(Throwable $e){
                    Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create all_day db error without file '.$e->getMessage());
                    return redirect(url('/school_app/all_day_school'))->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
                }  
            }
            Log::channel('stakeholders_microapps')->info(Auth::guard('school')->user()->name." create/update all_day success $month->name");
            return redirect(url('/school_app/all_day_school'))->with('success', "Τα στοιχεία για τον μήνα $month->name ενημερώθηκαν");
        }
        else{
            return redirect(url('/school_app/all_day_school'))->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
        }

    }

    public function download_file(Request $request, AllDaySchool $all_day_school){
       
        $file = 'all_day/all_day_'.$all_day_school->school->code.'_'.$all_day_school->month->id.'.xlsx';

        return Storage::disk('local')->download($file, $all_day_school->file);
    }

    public function update_all_day_template(Request $request){
        $rule = [
            'template_file' => 'required|mimes:xlsx'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return redirect()->back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
        }
        
        $path = $request->file('template_file')->storeAs('all_day', 'oloimero.xlsx');  
        
        return redirect()->back()->with('success', 'Το αρχείο ενημερώθηκε');
    }
}
