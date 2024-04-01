<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
{
    //
    public function import_sections(Request $request)
    {
        //validate the user's input
        $rule = [
            'import_sections' => 'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου (Επιτρεπτός τύπος: xlsx)');
        }

        //store the file
        $filename = "sections_file" . Auth::id() . ".xlsx";
        $path = $request->file('import_sections')->storeAs('files', $filename);

        //load the file with phpspreadsheet
        $mime = Storage::mimeType($path);
        $spreadsheet = IOFactory::load("../storage/app/$path");
        $schools_array = array();
        $row = 3;
        $rowSumValue = "1";
        $error = false;
        $done_at_least_once = false;
        $existingSections = [];
       
        //read the file and store the data (update existing or create new sections) in the database
        while ($rowSumValue != "" && $row < 10000) {
            $school_code = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(3, $row)->getValue();
            $section_name = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(10, $row)->getValue();
            $section_class = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(9, $row)->getValue();
            $section_code = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(11, $row)->getValue();
            $school = School::where('code', $school_code)->first();

            if ($school) {
                try {
                    $sec = Section::updateOrCreate(
                        [
                            'school_id' => $school->id,
                            'sec_code' => $section_code
                        ],
                        [
                            'name' => $section_name,
                            'class' => $section_class
                        ]
                    );
                    $existingSections[] = $school->id . '-' . $section_code;
                    if($sec->isDirty('name','class'))$done_at_least_once = true;
                } catch (Throwable $e) {
                    Log::channel('throwable_db')->error(Auth::user()->username . ' create section error ' . $school_code . ' ' . $e->getMessage());
                    $error = true;
                    continue;
                }
            } else {
                $error = true;
                Log::channel('throwable_db')->error(Auth::user()->username . ' create section unknown code ' . $school_code);
            }

            //change line and check if it's empty
            $row++;
            $rowSumValue = "";
            $rowSumValue = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(3, $row)->getValue();
        }

        //delete sections from db which are not in the xlsx
        $delete_those_which_are_not_in_xlsx = false;
        foreach(Section::all() as $section){
            if(!in_array($section->school_id . '-' . $section->sec_code, $existingSections)){
                $section->delete();
                $delete_those_which_are_not_in_xlsx = true;
            }
        }
        
        if ($done_at_least_once or $delete_those_which_are_not_in_xlsx) {
            DB::table('last_update_sections')->updateOrInsert(['id' => 1], ['date_updated' => now()]);
        }

        if (!$error) {
            return redirect(url('/sections'))->with('success', 'Επιτυχής ενημέρωση τμημάτων σχολείων');
        } else {
            return redirect(url('/sections'))->with('warning', 'Επιτυχής ενημέρωση τμημάτων σχολείων με σφάλματα που καταγράφηκαν στο log throwable_db');
        }
    }

    // public function delete_sections(Request $request){
    //     Section::truncate();
    //     return redirect(url('/sections'))->with('success', 'Τα τμήματα διαγράφηκαν');
    // }
}
