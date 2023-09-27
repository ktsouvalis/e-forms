<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
{
    //
    public function import_sections(Request $request){
        //validate the user's input
        $rule = [
            'import_sections' => 'required|mimes:xlsx'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return redirect(url('/'))->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
        }

        //store the file
        $filename = "sections_file".Auth::id().".xlsx";
        $path = $request->file('import_sections')->storeAs('files', $filename);

        //load the file with phpspreadsheet
        $mime = Storage::mimeType($path);
        $spreadsheet = IOFactory::load("../storage/app/$path");
        $schools_array=array();
        $row=3;
        $error=0;
        $rowSumValue="1";
        $error=false;
        while ($rowSumValue != "" && $row<10000){
            $school_code = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(3, $row)->getValue();
            $section_name = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(10, $row)->getValue();
            $section_class = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(9, $row)->getValue();
            $section_code = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(11, $row)->getValue();
            $school = School::where('code', $school_code)->first();
            if($school){    
                try{
                    Section::updateOrcreate(
                        [
                            'school_id'=>$school->id,
                            'sec_code'=>$section_code
                        ],
                        [
                            'school_id' => $school->id,
                            'name' => $section_name,
                            'class'=>$section_class,
                            'sec_code'=>$section_code  
                        ]
                    );
                }
                catch(Throwable $e){
                    Log::channel('throwable_db')->error(Auth::user()->username.' create section error '.$school_code.' '.$e->getMessage());
                    $error=true;
                    continue;    
                }
            }
            else{
                $error=true;
                Log::channel('throwable_db')->error(Auth::user()->username.' create section unknown code '.$school_code);    
            }
            
            //change line and check if it's empty
            $row++;
            $rowSumValue="";
            $rowSumValue = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(3, $row)->getValue();   
        }
        if(!$error)
            return redirect(url('/sections'))->with('success', 'Επιτυχής ενημέρωση τμημάτων σχολείων');
        else
            return redirect(url('/sections'))->with('warning', 'Επιτυχής ενημέρωση τμημάτων σχολείων με σφάλματα που καταγράφηκαν στο log throwable_db');
    }
}
