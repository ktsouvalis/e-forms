<?php

namespace App\Http\Controllers\microapps;

use App\Models\User;
use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\microapps\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Notifications\UserNotification;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
       // return view('microapps.enrollments.for_niki', ['appname' => 'enrollments']);
        return view('microapps.enrollments.index', ['appname' => 'enrollments']);
    }

    public function create(){
        return view('microapps.enrollments.create', ['appname' => 'enrollments']);
    }

    public function show($parameter){
        if($parameter == 'planning')
            return view('microapps.enrollments.planning');
        else{
            abort(404);
        }
    }

    public function save($select, Request $request){
        
        $rule = null;
        $school = Auth::guard('school')->user();
        if($request->file('file'))
            $filename = $request->file('file')->getClientOriginalName();
            
        //handle the case
        switch($select) {
            case 'enrolled':    //Υποβολή Αρχείου Εγγραφέντων Μαθητών ή/και Αριθμού Εγγραφέντων
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
            case 'total_students':  //Καταχώρηση Συνολικού Αριθμού Μαθητών για τον Προγραμματισμό του επόμενου σχ. έτος
                if($school->enrollments == null) return back()->with('failure', 'Πρέπει πρώτα να καταχωρήσετε τον αριθμό των μαθητών που εγγράφηκαν');
                $values = array(
                    'total_students_nr' => $request->input('total_students_nr')
                );
            break;
            case 'all_day':         //Καταχώρηση Αρχείου Μαθητών που θα φοιτήσουν Ολοήμερα το επόμενο σχ. έτος
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
            case 'all_day_next_year_planning':     //Καταχώρηση στοιχείων για τον Προγραμματισμό του επόμενου σχ. έτους ΔΗΜΟΤΙΚΑ: Αρχείο, Νηπιαγωγεία: Τιμές
                if($school->primary == 1){//Τα δημοτικά ανεβάζουν αρχείο
                    if($request->file('file')){
                        $rule = [
                            'file' => 'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ];
                    
                        $filename_to_store = "a1_a2_file_".$school->code.".xlsx";
                        $values = array(
                            'a1_a2_file' => $filename,
                        );
                    }
                    else {
                        return back()->with('failure', 'Πρέπει να ανεβάσετε το αρχείο');
                    }
                } else { //Τα Νηπιαγωγεία καταχωρούν τιμές
                    $sections = [];
                    if($request->input('nr_of_students_morning_zone') !== null){
                        $section = [];
                        $section['nr_of_students'] = $request->input('nr_of_students_morning_zone');
                        $section['nr_of_sections'] = $request->input('nr_of_sections_morning_zone');
                        $sections[] = $section;
                        $sections_json = json_encode($sections);
                        try{
                            EnrollmentsClasses::updateOrCreate(
                                 [
                                     'enrollment_id' => $school->enrollments->id,
                                 ],
                                 [
                                     'morning_zone_classes' => $sections_json,
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
                    }  
                    $sections = [];  
                    if($request->input('nr_of_students_all_day') !== null){
                        $section = [];
                        $section['nr_of_students'] = $request->input('nr_of_students_all_day');
                        $section['nr_of_sections'] = $request->input('nr_of_sections_all_day');
                        $sections[] = $section;
                    }
                    if($request->input('nr_of_students_extended_all_day') !== null){
                        $section = [];
                        $section['nr_of_students'] = $request->input('nr_of_students_extended_all_day');
                        $section['nr_of_sections'] = $request->input('nr_of_sections_extended_all_day');
                        $sections[] = $section;
                     
                    }
                    $sections_json = json_encode($sections);
                
                    try{
                        EnrollmentsClasses::updateOrCreate(
                             [
                                 'enrollment_id' => $school->enrollments->id,
                             ],
                             [
                                 'all_day_school_classes' => $sections_json,
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
                }    
            break;
            case 'extra_section':   //Καταχώρηση αρχείου για αίτημα δημιουργίας επιπλέον τμήματος
                if($school->enrollments == null) return back()->with('failure', 'Πρέπει πρώτα να καταχωρήσετε τον αριθμό των μαθητών που εγγράφηκαν');
                $rule = [
                    'file' => 'mimetypes:application/pdf|required'
                ];
                $filename_to_store = "enrollments3_".$school->code.".pdf";
                $values = array(
                    'extra_section_file1' => $filename
                ); 

            break;
            case 'boundary_students': //Καταχώρηση αρχείου για μαθητές στα όρια
                if($school->enrollments == null) return back()->with('failure', 'Πρέπει πρώτα να καταχωρήσετε τον αριθμό των μαθητών που εγγράφηκαν');
                $rule = [
                    'file' => 'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|required'
                ];
                $filename_to_store = "enrollments4_".$school->code.".xlsx";
                $values = array(
                    'boundaries_st_file1' => $filename
                ); 

            break;
            case 'update_parameters':   //Καταχώρηση Παραμέτρων από το διαχειριστή
                $values = array(
                    'schoolYear' => $request->input('schoolYear'),
                    'nextYearPlanningActive' => $request->input('nextYearPlanningActive'),
                    'nextYearPlanningAccepts' => $request->input('nextYearPlanningAccepts'),
                );
                $configPath = config_path('enrollments.php');
                $configContent = "<?php\n\nreturn " . var_export($values, true) . ";\n";
                File::put($configPath, $configContent);
                return back()->with('success', 'Οι παράμετροι αποθηκεύτηκαν');
            break;
            case 'nextYearNumbers':     //Καταχώρηση Αριθμού Μαθητών για το επόμενο σχ. έτος
                if(!$school->enrollments)
                    return back()->with('failure', 'Πρέπει πρώτα να καταχωρήσετε τον αριθμό των μαθητών που εγγράφηκαν');
                //dd($request->input());
                    $sections = [];
                for($i=1; $i<=6; $i++){
                    $section = [];
                    // if($request->input('leitourgikotita'.$i) !== null)
                    //     $section['leitourgikotita'] = $request->input('leitourgikotita'.$i);
                    if($request->input('nr_of_students'.$i) !== null)
                        $section['nr_of_students'] = $request->input('nr_of_students'.$i);
                    // υπολόγισε τον αριθμό των τμημάτων - αν είναι ολιγοθέσιο 1 αλλιώς ανάλογα με τον αριθμό των μαθητών

                    $section['nr_of_sections'] = $this->countNrOfSections($school->primary, $school->leitourgikotita, $request->input('nr_of_students'.$i));
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
        $readAllDayFileAndStore = '';
        if($school->primary == 1 && $select == 'all_day_next_year_planning' ){
            $readAllDayFileAndStore = $this->readAllDayFileAndStore();

        }
        //store
        if($this->microapp->accepts || config('enrollments.nextYearPlanningAccepts') == 1){
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
            if( $readAllDayFileAndStore != ''){
                $users = $this->microapp->users->where('can_edit', 1);
                foreach($users as $user){
                    $user->user->notify(new UserNotification(Auth::guard('school')->user()->name." ανέβασε αρχείο με πιθανό σφάλμα: ".$readAllDayFileAndStore, "Ειδοποίηση για πιθανά σφάλματα σε αρχειο."));
                }
                Log::channel('stakeholders_microapps')->error(Auth::guard('school')->user()->name." upload next years all day file with error: ".$readAllDayFileAndStore);
                return back()->with('failure', "Το αρχείο υποβλήθηκε με τις ακόλουθες επισημάνσεις: $readAllDayFileAndStore");
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

    public static function nextYearsLeitourgikotita($primary, $leitourgikotita, $total_students_nr){
        if($primary == 1) {//Δημοτικό
        
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
        } else { //Νηπιαγωγείο
            if($total_students_nr <= 25) //Θα λειτουργήσει 1θέσιο
                return 1;
            if($total_students_nr > 25 && $total_students_nr <= 50) //Θα λειτουργήσει 2θέσιο
                return 2;
            if($total_students_nr > 50 && $total_students_nr <= 75) // Θα λειτουργήσει 3θέσιο
                return 3;
            if($total_students_nr > 75 && $total_students_nr <= 100) // Θα λειτουργήσει 4θέσιο
                return 4;
            if($total_students_nr > 100 && $total_students_nr <= 125) // Θα λειτουργήσει 5θέσιο--}}
                return 5;
        }
    }

    public function countNrOfSections($primary, $leitourgikotita, $students_nr){
        if($primary == 1){
            if($leitourgikotita <= 6 )
                return 1;
            if($leitourgikotita > 6){
                switch ($students_nr) {
                    case $students_nr == 0:
                        return 0;
                    case $students_nr > 0 && $students_nr <= 25:
                        return 1;
                    case $students_nr > 25 && $students_nr <= 50:
                        return 2;
                    case $students_nr >= 50 && $students_nr <= 75:
                        return 3;
                    case $students_nr > 75 && $students_nr <= 100:
                        return 4;
                    case $students_nr > 100 && $students_nr <= 125:
                        return 5;
                }
            }
        } else {
            switch ($students_nr) {
                case $students_nr == 0:
                    return 0;
                case $students_nr > 0 && $students_nr <= 25:
                    return 1;
                case $students_nr > 25 && $students_nr <= 50:
                    return 2;
                case $students_nr >= 50 && $students_nr <= 75:
                    return 3;
                case $students_nr > 75 && $students_nr <= 100:
                    return 4;
                case $students_nr > 100 && $students_nr <= 125:
                    return 5;
            }
        }
    }

    private function readAllDayFileAndStore(){
        $school = Auth::guard('school')->user();
        $filename = "a1_a2_file_".$school->code.".xlsx";
        //load the file with phpspreadsheet
        $spreadsheet = IOFactory::load("../storage/app/enrollments/$filename");
        $spreadsheet->setActiveSheetIndex(0);
        //Έλεγχοι
        $check = ($spreadsheet->getActiveSheet()->getCellByColumnAndRow(3, 3)->getFormattedValue() == "")? 'Λείπει το όνομα του Σχολείου από την 3η γραμμή.        ' : '';
        if($school->has_extended_all_day == 1 && $spreadsheet->getActiveSheet()->getCellByColumnAndRow(7, 3)->getFormattedValue() != "Αναβαθμισμένο"){
            $check .= 'Το Σχολείο είναι ορισμένο ως διευρυμένο ολοήμερο. Η τιμή στο κελί G3 (τύπος ολοήμερου) πρέπει να είναι Αναβαθμισμένο.        ';
        }
        if($school->has_extended_all_day == 0 && $spreadsheet->getActiveSheet()->getCellByColumnAndRow(7, 3)->getFormattedValue() != "Κλασικό"){
            $check .= 'Το Σχολείο δεν είναι ορισμένο ως διευρυμένο ολοήμερο. Η τιμή στο κελί G3 (τύπος ολοήμερου) πρέπει να είναι Κλασικό.       ';
        }
        $morning_zone_nr_of_sections = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(6, 3)->getValue();
        $all_day_school_type = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(7, 3)->getValue();
        $all_day_sections_nr = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(8, 3)->getValue();
        
        $students_leaving_zone1 = ($all_day_school_type == 'Κλασικό')? $spreadsheet->getActiveSheet()->getCellByColumnAndRow(11, 3)->getValue() : $spreadsheet->getActiveSheet()->getCellByColumnAndRow(10, 3)->getValue();
        $students_leaving_zone2 = ($all_day_school_type == 'Κλασικό')? $spreadsheet->getActiveSheet()->getCellByColumnAndRow(13, 3)->getValue() : $spreadsheet->getActiveSheet()->getCellByColumnAndRow(12, 3)->getValue();
        $students_leaving_zone3 = ($all_day_school_type == 'Κλασικό')? 0 : $spreadsheet->getActiveSheet()->getCellByColumnAndRow(14, 3)->getValue();
        
        $all_day_students_nr = $students_leaving_zone1 + $students_leaving_zone2 + $students_leaving_zone3;
        $comments = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(14, 3)->getValue();
        //Αποθήκευσε τις τιμές
        $sections = [];
        $section = [];
        // Πρωινή ζώνη
        $section['nr_of_students'] = 0;
        $section['nr_of_sections'] = 0;
        if($morning_zone_nr_of_sections > 0){
            $section['nr_of_students'] = "";
            $section['nr_of_sections'] = $morning_zone_nr_of_sections;
        }
        $sections[] = $section;
        $sections_json = json_encode($sections);
        try{
            EnrollmentsClasses::updateOrCreate(
                [
                    'enrollment_id' => $school->enrollments->id
                ],
                [
                    'morning_zone_classes' => $sections_json
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

        $sections = [];
        //Ολοήμερα Ζώνη 1
        $section['nr_of_students'] = $all_day_students_nr;
        $section['nr_of_sections'] = ($all_day_students_nr%25 == 0)? $all_day_students_nr/25 : (int)floor($all_day_students_nr/25)+1;
        $sections[] = $section;
        
        //Ολοήμερα Ζώνη 2
        $section = [];
        $section['nr_of_students'] = 0;
        $section['nr_of_sections'] = 0;
        if($all_day_students_nr - $students_leaving_zone1 >= 0){
            $remaining_students_nr = $all_day_students_nr - $students_leaving_zone1;
            $section['nr_of_students'] = $remaining_students_nr;
            $section['nr_of_sections'] = ($remaining_students_nr%25 == 0)? $remaining_students_nr/25 : (int)floor($remaining_students_nr/25)+1;
        }
        $sections[] = $section;
        //Ολοήμερα Ζώνη 3
        $section = [];
        $section['nr_of_students'] = 0;
        $section['nr_of_sections'] = 0;
        if($remaining_students_nr - $students_leaving_zone2 >= 0){
            $remaining_students_nr = $remaining_students_nr - $students_leaving_zone2;
            $section['nr_of_students'] = $remaining_students_nr;
            $section['nr_of_sections'] = ($remaining_students_nr%25 == 0)? $remaining_students_nr/25 : (int)floor($remaining_students_nr/25)+1;
        }
        $sections[] = $section;
        $section = [];
        $sections_json = json_encode($sections);

        try{
            EnrollmentsClasses::updateOrCreate(
                [
                    'enrollment_id' => $school->enrollments->id
                ],
                [
                    'all_day_school_classes' => $sections_json
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
        return $check;
    }
}
