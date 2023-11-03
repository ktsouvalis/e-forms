<?php

namespace App\Http\Controllers\microapps;

use Throwable;
use App\Models\Month;
use App\Models\School;
use App\Models\Microapp;
use Illuminate\Http\Request;
use App\Models\microapps\Immigrant;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class ImmigrantsController extends Controller
{
    //
    public function post_immigrants(Request $request, School $school){
        $microapp = Microapp::where('url', '/immigrants')->first();
        if($microapp->accepts){
            
            $comments= $request->all()['comments'];
            
            $month = Month::getActiveMonth();
            if($request->file('table_file')){
                $rule = [
                    'table_file' => 'mimes:xlsx'
                ];
                $validator = Validator::make($request->all(), $rule);
                if($validator->fails()){ 
                    return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
                }
                $file = $request->file('table_file')->getClientOriginalName();
                //store the file
                $filename = "immigrants_".$school->code."_month".$month->id.".xlsx";
                try{
                    $path = $request->file('table_file')->storeAs('immigrants', $filename);
                }
                catch(Throwable $e){
                    Log::channel('stakeholders_microapps')->error(Auth::guard('school')->user()->name." create immigrants file error ".$e->getMessage());
                    return redirect(url('/school_app/immigrants'))->with('failure', 'Δεν έγινε η αποθήκευση του αρχείου, προσπαθήστε ξανά');     
                }

                try{
                    Immigrant::updateOrCreate(
                    [
                        'month_id'=>$month->id,
                        'school_id'=>$school->id
                    ],
                    [
                        'comments' => $comments,
                        'file' => $file
                    ]); 
                }
                catch(Throwable $e){
                    Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create immigrants db error '.$e->getMessage());
                    return redirect(url('/school_app/immigrants'))->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
                }
            }
            else{
                try{    
                    Immigrant::updateOrCreate(
                    [
                        'month_id'=>$month->id,
                        'school_id'=>$school->id
                    ],
                    [
                        'comments'=> $comments
                    ]); 
                }
                catch(Throwable $e){
                    Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create immigrants db error without file '.$e->getMessage());
                    return redirect(url('/school_app/immigrants'))->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
                }  
            }
            Log::channel('stakeholders_microapps')->info(Auth::guard('school')->user()->name." create/update immigrants success $month->name");
            return redirect(url('/school_app/immigrants'))->with('success', "Τα στοιχεία για τον μήνα $month->name ενημερώθηκαν");
        }
        else{
            return redirect(url('/school_app/immigrants'))->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
        }

    }

    public function download_file(Request $request, Immigrant $immigrant){
       
        $file = 'immigrants/immigrants_'.$immigrant->school->code.'_month'.$immigrant->month->id.'.xlsx';

        return Storage::disk('local')->download($file, $immigrant->file);
    }

    public function update_immigrants_template(Request $request){
        $rule = [
            'template_file' => 'required|mimes:xlsx'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
        }
        
        $path = $request->file('template_file')->storeAs('immigrants', 'immigrants.xlsx');  
        
        return back()->with('success', 'Το αρχείο ενημερώθηκε');
    }
}
