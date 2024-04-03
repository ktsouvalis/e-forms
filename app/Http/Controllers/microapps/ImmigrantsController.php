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
    private $microapp;

    public function __construct(){
        $this->middleware('auth')->only(['index']);
        $this->middleware('isSchool')->only(['create', 'store']);
        $this->microapp = Microapp::where('url', '/immigrants')->first();
    }

    public function index(){
        return view('microapps.immigrants.index', ['appname' => 'immigrants']); 
    }

    public function create(){
        return view('microapps.immigrants.create', ['appname' => 'immigrants']);
    }

    public function store(Request $request){
        $school = Auth::guard('school')->user();
        
        if($this->microapp->accepts){
            $comments= $request->all()['comments'];
            
            $month = Month::getActiveMonth();
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
                $filename = "immigrants_".$school->code."_month".$month->id.".xlsx";
                try{
                    $path = $request->file('table_file')->storeAs('immigrants', $filename);
                }
                catch(Throwable $e){
                    try{
                        Log::channel('stakeholders_microapps')->error(Auth::guard('school')->user()->name." create immigrants file error ".$e->getMessage());
                    }
                    catch(Throwable $e){
            
                    }
                    return back()->with('failure', 'Δεν έγινε η αποθήκευση του αρχείου, προσπαθήστε ξανά');     
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
                    try{
                        Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create immigrants db error '.$e->getMessage());
                    }
                    catch(Throwable $e){
            
                    }
                    return back()->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
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
                    try{
                        Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create immigrants db error without file '.$e->getMessage());
                    }
                    catch(Throwable $e){
            
                    }
                    return back()->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
                }  
            }
            try{
                Log::channel('stakeholders_microapps')->info(Auth::guard('school')->user()->name." create/update immigrants success $month->name");
            }
            catch(Throwable $e){
    
            }
            return back()->with('success', "Τα στοιχεία για τον μήνα $month->name ενημερώθηκαν");
        }
        else{
            return back()->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
        }

    }

    public function download_file(Request $request, Immigrant $immigrant){
        $immigrants_id = $this->microapp->id;
        if((Auth::check() && (Auth::user()->microapps->where('microapp_id', $immigrants_id)->count() or Auth::user()->isAdmin())) || (Auth::guard('school')->check() && Auth::guard('school')->user()->id == $immigrant->school->id)){
            $file = 'immigrants/immigrants_'.$immigrant->school->code.'_month'.$immigrant->month->id.'.xlsx';
            $response = Storage::disk('local')->download($file, $immigrant->file);  
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

    public function update_template(Request $request){
        $rule = [
            'template_file' => 'required|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου (Επιτρεπτός τύπος: xlsx)');
        }
        
        try{
            $path = $request->file('template_file')->storeAs('immigrants', 'immigrants.xlsx'); 
        }
        catch(Throwable $e){
            return back()->with('failure', 'Δεν ήταν δυνατή η αποθήκευση του αρχείου, προσπαθήστε ξανά');
        }
        
        return back()->with('success', 'Το αρχείο ενημερώθηκε');
    }

    public function download_template(Request $request){
        $file = 'immigrants/immigrants.xlsx';
        $response = Storage::disk('local')->download($file);  
        ob_end_clean();
        try{
            return $response;
        }
        catch(Throwable $e){
            return back()->with('failure', 'Δεν ήταν δυνατή η λήψη του αρχείου, προσπαθήστε ξανά');    
        }
    }
}
