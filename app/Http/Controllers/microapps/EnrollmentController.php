<?php

namespace App\Http\Controllers\microapps;

use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\microapps\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Validator;
use App\Models\microapps\EnrollmentsClasses;

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
            case 'total_students':
                if($school->enrollments == null) return back()->with('failure', 'Πρέπει πρώτα να καταχωρήσετε τον αριθμό των μαθητών που εγγράφηκαν');
                $values = array(
                    'total_students_nr' => $request->input('total_students_nr')
                );
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
            case 'update_parameters':
                $values = array(
                    'schoolYear' => $request->input('schoolYear'),
                    // 'date' => $request->input('date'),
                    'nextYearPlanning' => $request->input('nextYearPlanning')
                );
                $configPath = config_path('enrollments.php');
                $configContent = "<?php\n\nreturn " . var_export($values, true) . ";\n";
                File::put($configPath, $configContent);
                return back()->with('success', 'Οι παράμετροι αποθηκεύτηκαν');
            break;
            case 'nextYearNumbers':
                if(!$school->enrollments)
                    return back()->with('failure', 'Πρέπει πρώτα να καταχωρήσετε τον αριθμό των μαθητών που εγγράφηκαν');
                //dd($request->input());
                    $sections = [];
                for($i=1; $i<=6; $i++){
                    $section = [];
                    if($request->input('leitourgikotita'.$i) !== null)
                        $section['leitourgikotita'] = $request->input('leitourgikotita'.$i);
                    if($request->input('nr_of_students'.$i) !== null)
                        $section['nr_of_students'] = $request->input('nr_of_students'.$i);
                    if($request->input('comment'.$i) !== null)
                        $section['comment'] = $request->input('comment'.$i);
                    $sections[] = $section;
                }
                
                $sections = array_filter($sections, function ($section) {
                    return !empty($section);
                });
                $sections_json = json_encode($sections);
                try{
                   EnrollmentsClasses::updateOrCreate(
                        [
                            'enrollment_id' => $school->enrollments->id
                        ],
                        [
                            'morning_classes' => $sections_json
                        ]
                    );       
                } catch(Throwable $e){
                    try{
                        Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create enrollments db error '.$e->getMessage());
                    }
                    catch(Throwable $e){
            
                    }
                    return back()->with('failure', 'Η εγγραφή δεν αποθηκεύτηκε. Προσπαθήστε ξανά');
                }
                return back()->with('success', 'Η εγγραφή αποθηκεύτηκε.');
                          
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
        
        //store
        if($this->microapp->accepts){
            //dd($values);
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

    public function parameters_update(Request $request){
        dd('dfd');
        $configArray = [
            'schoolYear' => 2024,
            'date' => '2024-05-23',
            'phase_indicator' => 'alpha',
        ];
        
    }

    public static function nextYearsLeitourgikotita($leitourgikotita, $total_students_nr){
        if($leitourgikotita < 6){
            if($total_students_nr <=15) //Θα λειτουργήσει 1θέσιο
                return 1;
            if($total_students_nr > 15 && $total_students_nr <= 30) //Θα λειτουργήσει 2θέσιο
                return 2;
            if($total_students_nr > 30 && $total_students_nr <= 45) // Θα λειτουργήσει 3θέσιο
                return 3;
            if($total_students_nr > 45 && $total_students_nr <= 60) // Θα λειτουργήσει 4θέσιο
                return 4;
            if($total_students_nr > 61 && $total_students_nr <= 75) // Θα λειτουργήσει 5θέσιο--}}
                return 5;
        } else { // Είναι 6θέσιο και άνω --}}
            return $leitourgikotita;
        } 
    }
}
