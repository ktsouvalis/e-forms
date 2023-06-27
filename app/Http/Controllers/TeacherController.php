<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Directory;
use Illuminate\Http\Request;
use App\Models\SxesiErgasias;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    //
    public function test(Teacher $teacher){
        return view('test',['teacher'=>$teacher]);
    }

    public function makeForm(Form $form){
    
        return view('teacher_view', ['form' => $form]);
    }

    public function login($md5){ 
        $msg = "Δε βρέθηκε η σελίδα που ζητήσατε";
        $state="failure";
        $teacher = Teacher::where('md5', $md5)->first();
        if($teacher){
            Auth::guard('teacher')->login($teacher);
            session()->regenerate();
            $msg=$teacher->name." καλωσήρθατε";
            $state = 'success';
        }
        return redirect(url('/index_teacher'))->with($state,$msg);
    }

    public function logout(){
        // $files = Storage::disk('public')->files('temp');
        // foreach($files as $file_p){
        //     if(strpos(basename($file_p), auth()->guard('teacher')->user()->afm)!==false){
        //         Storage::disk('public')->delete($file_p);
        //     }
        // }
        auth()->guard('teacher')->logout();
        return redirect(url('/index_teacher'))->with('success', 'Αποσυνδεθήκατε');
    }

    public function importTeachers(Request $request){
        $rule = [
            'import_teachers_organiki' => 'required|mimes:xlsx'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return redirect(url('/'))->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
        }

        $filename = "teachers_organiki_file".Auth::id().".xlsx";
        $path = $request->file('import_teachers_organiki')->storeAs('files', $filename);
        $mime = Storage::mimeType($path);
        $spreadsheet = IOFactory::load("../storage/app/$path");
        $teachers_array=array();
        $row=2;
        $error=0;
        $rowSumValue="1";
        while ($rowSumValue != "" && $row<10000){
            $check=array();
    
            $check['name'] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(5, $row)->getValue();
            $check['surname']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(4, $row)->getValue();
            $check['fname']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(6, $row)->getValue();
            $check['mname']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(7, $row)->getValue();

            $afm = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2, $row)->getValue();
            $check['afm']= substr($afm, 2, -1); // remove from start =" and remove from end "

            $check['gender']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(3, $row)->getValue();
            $check['telephone']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(11, $row)->getValue();
            $check['mail']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(13, $row)->getValue();
            $check['sch_mail']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(14, $row)->getValue();
            $check['klados']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(15, $row)->getValue();
            $check['am']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();
            
            $sxesi = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(48, $row)->getValue();
            if(SxesiErgasias::where('name',$sxesi)->count()){
                $check['sxesi_ergasias'] = SxesiErgasias::where('name',$sxesi)->first()->id;
                $check['sxesi_ergasias_name'] = SxesiErgasias::where('name',$sxesi)->first()->name;
            }
            else{
                $error= 1;
                $check['sxesi_ergasias'] = "Error: Κενό πεδίο";
            }

            // $check['action']="";
            // if(Teacher::where('afm', $check['afm'])->count()){
            //     $check['action']=Teacher::where('afm', $check['afm'])->first()->id;
            // }
            $ignore_record =0;
            if($request->input('template_file')=='organiki'){

                $organiki = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(36, $row)->getValue();
                $sanitized_organiki = substr($organiki, 2, -1); // remove from start =" and remove from end "

                $check['org_eae']=1;
                if($spreadsheet->getActiveSheet()->getCellByColumnAndRow(54, $row)->getValue()=="ΟΧΙ"){
                    $check['org_eae']=0;
                }

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
            else if($request->input('template_file')=='apospasi'){
                
                $check['org_eae']=1;
                if($spreadsheet->getActiveSheet()->getCellByColumnAndRow(50, $row)->getValue()=="ΟΧΙ"){
                    $check['org_eae']=0;
                }

                $organiki = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(23, $row)->getValue();
                if($organiki!="ΑΧΑΪΑΣ (Π.Ε.) 2018"){
                    // $organiki =substr($organiki, 0, -5); //delete 2018
                    // $pe_de = substr($organiki, -7, -1); //keep P.E. or D.E.
                    // $organiki = substr($organiki, 0, -9); //delete P.E./D.E.
                    // $organiki = "ΔΙΕΥΘΥΝΣΗ (".$pe_de.") ".$organiki;
                    
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
                    $ignore_record =1;   
                }
            }
            if(!$ignore_record)array_push($teachers_array, $check);
            $row++;
            $rowSumValue="";
            for($col=1;$col<=54;$col++){
                $rowSumValue .= $spreadsheet->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();   
            }
        }

        session(['teachers_array' => $teachers_array]);

        if($error){
            return redirect(url('/import_teachers'))
                ->with('asks_to','error');
        }else{
            return redirect(url('/import_teachers'))
                ->with('asks_to','save');
        }
    }


    public function insertTeachers(){
        $teachers_array = session('teachers_array');
        session()->forget('teachers_array');


        // // DELETE TEACHERS FROM DATABASE THAT ARE NOT IN XLSX
        // $afms = array_column($teachers_array, 'afm');
        // $recordsToDelete = Teacher::whereNotIn('afm', $afms)->get();
        // foreach($recordsToDelete as $record){
        //     $record->delete();
        // }

        // CREATE OR UPDATE EXISTING TEACHERS
        foreach($teachers_array as $teacher){
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
        return redirect(url('/teachers'))
        ->with('success', 'Η εισαγωγή ολοκληρώθηκε');
    }
}