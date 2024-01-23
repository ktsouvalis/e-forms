<?php

namespace App\Http\Controllers\microapps;

use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\microapps\Defibrillator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DefibrillatorsController extends Controller
{
    //
    public function save_defibrillators(Request $request){
        $school = Auth::guard('school')->user();
        $microapp = Microapp::where('url', '/defibrillators')->first();
        if($microapp->accepts){
            $comments= $request->all()['comments'];
            
            if($request->file('record_file')){
                $rule = [
                    'record_file' => 'mimetypes:application/pdf'
                ];
                $validator = Validator::make($request->all(), $rule);
                if($validator->fails()){ 
                    return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου (Επιτρεπτός τύπος: pdf)');
                }
                $file = $request->file('record_file')->getClientOriginalName();
                //store the file
                $filename = "defibrillators_".$school->code.".pdf";
                try{
                    $path = $request->file('record_file')->storeAs('defibrillators', $filename);
                }
                catch(Throwable $e){
                    try{
                        Log::channel('stakeholders_microapps')->error(Auth::guard('school')->user()->name." create defibrillators file error ".$e->getMessage());
                    }
                    catch(Throwable $e){
            
                    }
                    return redirect(url('/school_app/defibrillators'))->with('failure', 'Δεν έγινε η αποθήκευση του αρχείου, προσπαθήστε ξανά');     
                }

                try{
                    Defibrillator::updateOrCreate(
                    [
                        'school_id'=>$school->id
                    ],
                    [
                        'comments' => $comments,
                        'file' => $file
                    ]); 
                }
                catch(Throwable $e){
                    try{
                        Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create defibrillators db error '.$e->getMessage());
                    }
                    catch(Throwable $e){
            
                    }
                    return redirect(url('/school_app/defibrillators'))->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
                }
            }
            else{
                try{    
                    Defibrillator::updateOrCreate(
                    [
                        'school_id'=>$school->id
                    ],
                    [
                        'comments'=> $comments
                    ]); 
                }
                catch(Throwable $e){
                    try{
                        Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create defibrillators db error without file '.$e->getMessage());
                    }
                    catch(Throwable $e){
            
                    }
                    return redirect(url('/school_app/defibrillators'))->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
                }  
            }
            try{
                Log::channel('stakeholders_microapps')->info(Auth::guard('school')->user()->name." create/update defibrillators success");
            }
            catch(Throwable $e){
    
            }
            return redirect(url('/school_app/defibrillators'))->with('success', "Τα στοιχεία ενημερώθηκαν");
        }
        else{
            return redirect(url('/school_app/defibrillators'))->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
        }

    }

    public function download_file(Request $request, Defibrillator $defibrillator){
        $defibrillator_id = Microapp::where('url', '/defibrillators')->first()->id;
        if((Auth::check() && (Auth::user()->microapps->where('microapp_id', $defibrillator_id)->count() or Auth::user()->isAdmin())) || (Auth::guard('school')->check() && Auth::guard('school')->user()->id == $defibrillator->school->id)){
            $file = 'defibrillators/defibrillators_'.$defibrillator->school->code.'.pdf';
            $response = Storage::disk('local')->download($file, $defibrillator->file);  
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
}
