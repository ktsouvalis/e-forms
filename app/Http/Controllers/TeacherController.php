<?php

namespace App\Http\Controllers;

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

    public function imporTeachersOrganiki(Request $request){
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
        $update=array();
        $row=2;
        $error=0;
        $rowSumValue="1";
        while ($rowSumValue != "" && $row<10000){
            $check=array();
            $update=0;
            $check['name'] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(5, $row)->getValue();
            $check['surname']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(4, $row)->getValue();
            $check['fname']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(6, $row)->getValue();
            $check['mname']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(7, $row)->getValue();

            $afm = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2, $row)->getValue();
            $check['afm']= substr($afm, 2, -1);

            $check['gender']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(3, $row)->getValue();
            $check['telephone']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(11, $row)->getValue();
            $check['mail']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(13, $row)->getValue();
            $check['sch_mail']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(14, $row)->getValue();
            $check['klados']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(15, $row)->getValue();
            $check['am']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();

            $organiki = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(36, $row)->getValue();
            $check['organiki'] = substr($organiki, 2, -1);
            
            
            if(Teacher::where('afm', $check['afm'])->count()){
                $update[Teacher::where('afm', $check['afm'])->first()->id]=1;
            }

            $check['org_eae']=1;
            if($spreadsheet->getActiveSheet()->getCellByColumnAndRow(54, $row)->getValue()=="ΟΧΙ"){
                $check['org_eae']=0;
            }

            $sxesi = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(48, $row)->getValue();
            if(SxesiErgasias::where('name',$sxesi)->count()){
                $check['sxesi_ergasias'] = SxesiErgasias::where('name',$sxesi)->first()->id;
            }
            else{
                $error= 1;
                $check['sxesi_ergasias'] = "Κενό πεδίο";
            }

            $organiki = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(36, $row)->getValue();
            if(School::where('code', $organiki)->count()){
                $check['organiki'] = School::where('code', $organiki)->get()->id;
                $check['organiki_type'] = "App\Model\School";
            }
            else if(Directory::where('code', $organiki)->count()){
                $check['organiki'] = Directory::where('code', $organiki)->get()->id;
                $check['organiki_type'] = "App\Model\Directory";    
            }
            else{
                $error=1;
                $check['organiki'] = "Άγνωστος κωδικός οργανικής";
            }

            array_push($teachers_array, $check);
            $row++;
            $rowSumValue="";
            for($col=1;$col<=54;$col++){
                $rowSumValue .= $spreadsheet->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();   
            }
        }

        session(['teachers_array' => $teachers_array]);
        session(['active_tab' =>'import']);
        session(['update' => $update]);

        if($error){
            return redirect(url('/teachers'))
                ->with('asks_to','error');
        }else{
            return redirect(url('/teachers'))
                ->with('asks_to','save');
        }
    }
}