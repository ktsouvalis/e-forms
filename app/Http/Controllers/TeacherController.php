<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Form;
use App\Models\School;
use App\Models\Teacher;
use App\Models\NoSchool;
use App\Models\Directory;
use Illuminate\Http\Request;
use App\Models\SxesiErgasias;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    /**
     * Return the generic view for forms
     *
     * @param Form $form The form model instance.
     * @return \Illuminate\View\View The rendered view.
     */
    public function makeForm(Form $form){
    
        return view('teacher_view', ['form' => $form]);
    }

    public function login($md5){ 
       
        $teacher = Teacher::where('md5', $md5)->firstOrFail();
        //logs the teacher in using the 'teacher' guard
        Auth::guard('teacher')->login($teacher);
        session()->regenerate();
        $teacher->logged_in_at = Carbon::now();   
        $teacher->save();

        return redirect(url('/index_teacher'))->with('success',"$teacher->name καλωσήρθατε!");
    }

    public function logout(){
        auth()->guard('teacher')->logout();
        return redirect(url('/index_teacher'))->with('success', 'Αποσυνδεθήκατε');
    }

    /**
     * Import and read the xlsx file
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function importTeachers(Request $request){

        //validate the input file type
        $rule = [
            'import_teachers' => 'required|mimes:xlsx'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return redirect(url('/'))->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
        }

        //store the file
        $filename = "teachers_file".Auth::id().".xlsx";
        $path = $request->file('import_teachers')->storeAs('files', $filename);

        //load the file
        $mime = Storage::mimeType($path);
        $spreadsheet = IOFactory::load("../storage/app/$path");

        //iterate inside the xlsx line by line
        $teachers_array=array();
        $row=2;
        $error=0;
        $rowSumValue="1";
        if($request->input('template_file')=='didaskalia'){
            $error_did=false;
            while ($rowSumValue != "" && $row<10000){
                $afm = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(18, $row)->getValue();
                $teacher_afm = substr($afm,2,-1); // remove from start =" and remove from end "
                $code = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(8, $row)->getValue();
                $school_code = substr($code,2,-1); // remove from start =" and remove from end "
                if(!Teacher::where('afm', $teacher_afm)->count()){
                    $row++;
                    Log::channel('throwable_db')->error('update didaskalia afm error: '.$teacher_afm);
                    $error_did=true;
                    continue;
                }
                else{
                    $teacher = Teacher::where('afm', $teacher_afm)->first();
                    if(!School::where('code', $school_code)->count()){
                        $row++;
                        Log::channel('throwable_db')->error('update didaskalia code error: '.$school_code);
                        $error_did=true;
                        continue;
                    }
                    else{
                        $school = School::where('code', $school_code)->first();
                        
                        $teacher->ypiretisi_id = $school->id;
                        $teacher->ypiretisi_type = "App\Models\School";
                        $teacher->save();
                        // Log::channel('user_memorable_actions')->info(Auth::user()->username." ok ". $teacher->afm);
                    }
                    
                }
                
                //change line and check if it's empty
                $row++;
                $rowSumValue="";
                for($col=1;$col<=54;$col++){
                    $rowSumValue .= $spreadsheet->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();   
                }
            }
            if(!$error_did)
                return redirect(url('/teachers'))->with('success', 'Επιτυχής ενημέρωση 1ου σχολείου υπηρέτησης εκπαιδευτικών');
            else
                return redirect(url('/teachers'))->with('warning', 'Επιτυχής ενημέρωση 1ου σχολείου υπηρέτησης εκπαιδευτικών με σφάλματα που καταγράφηκαν στο log throwable_db');
        }
        else if($request->input('template_file')=='apousia'){
            $error_ap=false;
            while ($rowSumValue != "" && $row<10000){
                $afm = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(18, $row)->getValue();
                $teacher_afm = substr($afm,2,-1); // remove from start =" and remove from end "
                $apousia = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(46, $row)->getValue();
                
                if(!Teacher::where('afm', $teacher_afm)->count()){
                    $row++;
                    Log::channel('throwable_db')->error('update apousia afm error: '.$teacher_afm);
                    $error_ap=true;
                    continue;
                }
                else{
                    $teacher = Teacher::where('afm', $teacher_afm)->first();
                    if(!NoSchool::where('name', $apousia)->count()){
                        $row++;
                        Log::channel('throwable_db')->error('update apousia no_school error: '.$apousia);
                        $error_ap=true;
                        continue;
                    }
                    else{
                        $no_school = NoSchool::where('name', $apousia)->first();
                        $teacher->ypiretisi_id = $no_school->id;
                        $teacher->ypiretisi_type = "App\Models\NoSchool";
                        $teacher->save();
                    } 
                }
                
                //change line and check if it's empty
                $row++;
                $rowSumValue="";
                for($col=1;$col<=54;$col++){
                    $rowSumValue .= $spreadsheet->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();   
                }
            }
            if(!$error_ap)
                return redirect(url('/teachers'))->with('success', 'Επιτυχής ενημέρωση απουσίας εκπαιδευτικών'); 
            else
                return redirect(url('/teachers'))->with('warning', 'Επιτυχής ενημέρωση απουσίας εκπαιδευτικών με σφάλματα που καταγράφηκαν στο log throwable_db');      
        }   
        else{
            while ($rowSumValue != "" && $row<10000){
                $check=array();
        
                $check['name'] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(5, $row)->getValue();
                $check['surname']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(4, $row)->getValue();
                $check['fname']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(6, $row)->getValue();
                $check['mname']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(7, $row)->getValue();
                
                //myschool stores the afm like eg "=999999999"
                $afm = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2, $row)->getValue();
                $check['afm']= substr($afm, 2, -1); // remove from start =" and remove from end "

                //check obvious fields
                $check['gender']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(3, $row)->getValue();
                $check['telephone']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(11, $row)->getValue();
                $check['mail']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(13, $row)->getValue();
                $check['sch_mail']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(14, $row)->getValue();
                $check['klados']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(15, $row)->getValue();
                $check['am']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();
                
                //cross check sxesi_ergasias with database
                $sxesi = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(48, $row)->getValue();
                if(SxesiErgasias::where('name',$sxesi)->count()){
                    $check['sxesi_ergasias'] = SxesiErgasias::where('name',$sxesi)->first()->id;
                    $check['sxesi_ergasias_name'] = SxesiErgasias::where('name',$sxesi)->first()->name;
                }
                else{
                    $error = 1;
                    $check['sxesi_ergasias'] = "Error: Κενό πεδίο";
                }

                $ignore_record = 0;
                if($request->input('template_file')=='organiki'){ // myschool report 4.1

                    //myschool stores the organiki like eg "=999999999"
                    $organiki = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(36, $row)->getValue();
                    $sanitized_organiki = substr($organiki, 2, -1); // remove from start =" and remove from end "

                    $check['org_eae']=1;
                    if($spreadsheet->getActiveSheet()->getCellByColumnAndRow(54, $row)->getValue()=="ΟΧΙ"){
                        $check['org_eae']=0;
                    }

                    //check if organiki is in a school (Σχολείο) or Directory (Διεύθυνση Εκπαίδευσης)
                    if(School::where('code', $sanitized_organiki)->count()){
                        $check['organiki'] = School::where('code', $sanitized_organiki)->first()->id;
                        $check['organiki_name'] = School::where('code', $sanitized_organiki)->first()->name;
                        $check['organiki_type'] = "App\Models\School";
                    }
                    else if(Directory::where('code', $sanitized_organiki)->count()){
                        $check['organiki'] = Directory::where('code', $sanitized_organiki)->first()->id;
                        $check['organiki_name'] = Directory::where('code', $sanitized_organiki)->first()->name;
                        $check['organiki_type'] = "App\Models\Directory";    
                    }
                    else{
                        $error=1;
                        $check['organiki'] = "Error: Άγνωστος κωδικός οργανικής";
                    }
                }
                else if($request->input('template_file')=='apospasi'){ // myschool report 4.2
                    
                    $check['org_eae']=1;
                    if($spreadsheet->getActiveSheet()->getCellByColumnAndRow(50, $row)->getValue()=="ΟΧΙ"){
                        $check['org_eae']=0;
                    }

                    //fix  directories to match database and then cross check
                    $organiki = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(23, $row)->getValue();
                    if($organiki!="ΑΧΑΪΑΣ (Π.Ε.) 2018"){
                        
                        // Extract the parts using regular expressions
                        preg_match('/^(\S+[^ \d])?(.*?) \((.*?)\)/', $organiki, $matches);
                        // Check if matches[1] is in the format of "Α'", "Β'", etc., and matches[2] is "ΑΘΗΝΑΣ"
                        if ($matches[2]!=''){
                            if($matches[2] == ' ΑΘΗΝΑΣ') {
                            // Rearrange the parts to the desired format
                            $newString = 'ΔΙΕΥΘΥΝΣΗ ' . $matches[3] . ' ' . trim($matches[1] . $matches[2]);
                            }
                            else{
                                if($matches[2]==" ΘΕΣΣΑΛΟΝΙΚΗΣ"){
                                    // echo 'true';
                                    if($matches[1]=="Α΄")$matches[2]="ΑΝΑΤ. ΘΕΣ/ΝΙΚΗΣ";
                                    if($matches[1]=="Β΄")$matches[2]="ΔΥΤ. ΘΕΣ/ΝΙΚΗΣ";
                                }
                                if($matches[2]==" ΑΤΤΙΚΗΣ")$matches[2]="ΔΥΤΙΚΗΣ ΑΤΤΙΚΗΣ";
                                if($matches[2]==" ΑΝΑΤ. ΑΤΤΙΚΗΣ")$matches[2]="ΑΝΑΤΟΛΙΚΗΣ ΑΤΤΙΚΗΣ";
                                $newString = 'ΔΙΕΥΘΥΝΣΗ ' . $matches[3] . ' '. trim($matches[2]);    
                            }
                        }
                        else {
                            // Rearrange the parts to the desired format without keeping matches[2]
                            $newString = 'ΔΙΕΥΘΥΝΣΗ ' . $matches[3] . ' '. $matches[1];
                        }
                        
                        if(Directory::where('name', $newString)->count()){
                            $check['organiki'] = Directory::where('name', $newString)->first()->id;
                            $check['organiki_name'] = Directory::where('name', $newString)->first()->name;
                            $check['organiki_type'] = "App\Models\Directory";
                        }
                        else{
                            $error=1;
                            $check['organiki'] = "Error: Άγνωστος κωδικός οργανικής";
                        }
                    }
                    else{
                        $ignore_record = 1;  //ignore those that belongs to ΑΧΑΪΑ because they are in the database through the 4.1 report (organiki) 
                    }
                }
                //prepare teachers_array for session
                if(!$ignore_record)array_push($teachers_array, $check);

                //change line and check if it's empty
                $row++;
                $rowSumValue="";
                for($col=1;$col<=54;$col++){
                    $rowSumValue .= $spreadsheet->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();   
                }
            }
        }
        //push the prepared array in session for later use
        session(['teachers_array' => $teachers_array]);

        if($error){
            return redirect(url('/import_teachers'))
                ->with('asks_to','error');
        }else{
            return redirect(url('/import_teachers'))
                ->with('asks_to','save');
        }
    }

    /**
     * Insert teachers from session to database
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function insertTeachers(){
        //read the teachers_array which is prepared from the importTeachers() method
        $teachers_array = session('teachers_array');
        session()->forget('teachers_array');
        $error=false;
        // CREATE OR UPDATE (based on 'afm' field) EXISTING TEACHERS
        foreach($teachers_array as $teacher){
            try{
                Teacher::updateOrcreate(
                    [
                        'afm'=> $teacher['afm'] 
                    ],
                    [
                        'md5' => md5($teacher['afm']),
                        'name'=> $teacher['name'],
                        'surname'=> $teacher['surname'],
                        'fname' => $teacher['fname'],
                        'mname' => $teacher['mname'],
                        'afm' => $teacher['afm'],
                        'gender' => $teacher['gender'],
                        'telephone' => $teacher['telephone'],
                        'mail' => $teacher['mail'],
                        'sch_mail' => $teacher['sch_mail'],
                        'klados' => $teacher['klados'],
                        'am' => $teacher['am'],
                        'sxesi_ergasias_id' => $teacher['sxesi_ergasias'],
                        'org_eae' => $teacher['org_eae'],
                        'organiki_id' => $teacher['organiki'],
                        'organiki_type' => $teacher['organiki_type']
                    ]
                );
            }
            catch(Throwable $e){
                Log::channel('throwable_db')->error(Auth::user()->username.' create teacher error '.$teacher['afm']);
                $error=true;
                continue; 
            }
        }
        if(!$error){
            Log::channel('user_memorable_actions')->info(Auth::user()->username.' insertTeachers');
            return redirect(url('/teachers'))
                ->with('success', 'Η εισαγωγή ολοκληρώθηκε');
        }
        else{
            Log::channel('user_memorable_actions')->warning(Auth::user()->username.' insertTeachers with errors');
            return redirect(url('/teachers'))
                ->with('warning', 'Η εισαγωγή ολοκληρώθηκε με σφάλματα που καταγράφηκαν στο log throwable_db');
        }
    }
}