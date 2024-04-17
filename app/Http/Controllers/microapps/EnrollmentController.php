<?php

namespace App\Http\Controllers\microapps;

use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\microapps\Enrollment;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Validator;

class EnrollmentController extends Controller
{
    //
    private $microapp;

    public function __construct(){
        $this->middleware('auth')->only(['index']);
        $this->middleware('isSchool')->only(['create']);
        $this->microapp = Microapp::where('url', '/enrollments')->first();
    }

    public function index(){
        return view('microapps.enrollments.index', ['appname' => 'enrollments']);
    }

    public function create(){
        return view('microapps.enrollments.create', ['appname' => 'enrollments']);
    }

    public function save($select, Request $request){
        $rule = null;
        $school = Auth::guard('school')->user();
        if($request->file('file'))
            $filename = $request->file('file')->getClientOriginalName();
            
        //handle the file
        switch($select) {
            case 'enrolled':
                if($request->file('file')){
                    $rule = [
                        'file' => 'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ];
                    $filename_to_store = "enrollments1_".$school->code.".xlsx";
                    $values = array(
                        'nr_of_students1' => $request->input('nr_of_students1'),
                        'enrolled_file1' => $filename
                    );
                }else {
                    $values = array(
                        'nr_of_students1' => $request->input('nr_of_students1')
                    );
                } 
            break;
            case 'all_day':
                if($school->enrollments == null) return back()->with('failure', 'Πρέπει πρώτα να καταχωρήσετε τον αριθμό των μαθητών που εγγράφηκαν');
                if($request->file('file')){
                    $rule = [
                        'file' => 'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ];
                
                    $filename_to_store = "enrollments2_".$school->code.".xlsx";
                    $values = array(
                        'nr_of_students1_all_day1' => $request->input('nr_of_students1_all_day1'),
                        'all_day_file1' => $filename
                    ); 
                }
                else {
                    $values = array(
                        'nr_of_students1_all_day1' => $request->input('nr_of_students1_all_day1')
                    );
                }

            break;
            case 'extra_section':
                if($school->enrollments == null) return back()->with('failure', 'Πρέπει πρώτα να καταχωρήσετε τον αριθμό των μαθητών που εγγράφηκαν');
                $rule = [
                    'file' => 'mimetypes:application/pdf|required'
                ];
                $filename_to_store = "enrollments3_".$school->code.".pdf";
                $values = array(
                    'extra_section_file1' => $filename
                ); 

            break;
            case 'boundary_students':
                if($school->enrollments == null) return back()->with('failure', 'Πρέπει πρώτα να καταχωρήσετε τον αριθμό των μαθητών που εγγράφηκαν');
                $rule = [
                    'file' => 'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|required'
                ];
                $filename_to_store = "enrollments4_".$school->code.".xlsx";
                $values = array(
                    'boundaries_st_file1' => $filename
                ); 

            break;
            default:
                return back()->with('failure', 'Κάτι δεν πήγε καλά. Δοκιμάστε ξανά.');
            break;
        }
        if($rule){
            $validator = Validator::make($request->all(), $rule);
            if($validator->fails()){ 
                return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
            }
            try{
                $path = $request->file('file')->storeAs('enrollments', $filename_to_store);
            }
            catch(Throwable $e){
                try{
                    Log::channel('stakeholders_microapps')->error(Auth::guard('school')->user()->name." create enrollments file error ".$e->getMessage());
                }
                catch(Throwable $e){
        
                }
                return back()->with('failure', 'Δεν έγινε η αποθήκευση του αρχείου, προσπαθήστε ξανά');     
            }
        }
        
        //store the file
       
        
        if($this->microapp->accepts){
            try{
                Enrollment::updateOrCreate(
                    [
                        'school_id'=>$school->id
                    ],
                    $values
                );
            }
            catch(Throwable $e){
                try{
                    Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create enrollments db error '.$e->getMessage());
                }
                catch(Throwable $e){
        
                }
                return back()->with('failure', 'Η εγγραφή δεν αποθηκεύτηκε. Προσπαθήστε ξανά');
            }
            if($this->microapp->stakeholders->count()){
                $stakeholder = $this->microapp->stakeholders->where('stakeholder_id', $school->id)->where('stakeholder_type', 'App\Models\School')->first();
                $stakeholder->hasAnswer = 1;
                $stakeholder->save();
            }
            return back()->with('success', 'Η εγγραφή αποθηκεύτηκε.');
        }
        else{
            return back()->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
        }
        
    }

    public function upload_file(Request $request, $upload_file_name){//app_use
        $error=false;
        $directory = 'enrollments';
        $file = $request->file('file');
        // store  file
        $fileHandler = new FilesController();
        $upload  = $fileHandler->upload_file($directory, $file, 'local', $upload_file_name);
        if($upload->getStatusCode() == 500){
            $error=true;
        }
        if(!$error)
            return back()->with('success', 'Το αρχείο ανέβηκε επιτυχώς');
        else
            return back()->with('failure', 'Προσπαθήστε ξανά');
    }

    public function download_file($file, $download_file_name = null){
        $username = Auth::check() ? Auth::user()->username : (Auth::guard('school')->check() ? Auth::guard('school')->user()->name : Auth::guard('teacher')->user()->afm);
        $directory = "enrollments";
        $fileHandler = new FilesController();
        $download = $fileHandler->download_file($directory, $file, 'local', $download_file_name);
        if($download->getStatusCode() == 500){
            Log::channel('files')->error($username." File $file failed to download");
            return back()->with('failure', 'Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($username." File $file successfully downloaded");
        return $download;
    }
}
