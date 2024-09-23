<?php

namespace App\Http\Controllers\microapps;

use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\microapps\Timetables;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FilesController;
use App\Models\microapps\TimetablesFiles;

class TimetablesController extends Controller
{
    //
    private $microapp;

    public function __construct(){
        $this->middleware('auth')->only(['index']);
        $this->middleware('isSchool')->only(['create']);
        $this->microapp = Microapp::where('url', '/timetables')->first();
    }

    public function index(){
       // return view('microapps.enrollments.for_niki', ['appname' => 'enrollments']);
        return view('microapps.timetables.index', ['appname' => 'timetables']);
    }

    public function create(){
        return view('microapps.timetables.create', ['appname' => 'timetables']);
    }

    public function upload_files(Request $request, Timetables $timetable){
        //Βρες αν υπάρχει Ωρολόγιο Πρόγραμμα σε κατάσταση επεξεργασίας. 
        $timetable = Timetables::where('school_id', Auth::guard('school')->user()->id)->where('status', 0)->first();
        if($timetable){
            if(Auth::guard('school')->user()->id != $timetable->school_id){
                return back()->with('failure', 'Δεν έχετε δικαίωμα πρόσβασης.');
            }
        }
        $request->validate([ //Έλεγξε τον τύπο των αρχείων και το μέγεθός τους
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,xlsx,xls|max:2048',
        ]);
        $files = $request->file('files');
        $fileNames = [];
        //Αν δεν υπάρχει, δημιούργησε ένα νέο Ωρολόγιο Πρόγραμμα
        if(!$timetable){
            $timetable = new Timetables();
            $timetable->school_id = Auth::guard('school')->user()->id;
            $timetable->status = 0;
            $timetable->save();
        }
        //Βρες πόσα αρχεία έχει ήδη ανεβάσει
        $timetableId = $timetable->id;
        $timetableFiles = TimetablesFiles::where('timetable_id', $timetableId)->first();
        if($timetableFiles && count($timetableFiles)>0){ // Αν έχει ανεβάσει ήδη, βρες τον αριθμό του τελευταίου αρχείου από το όνομά του
            $fileNames = json_decode($timetableFiles->filenames_json, true);
            end($fileNames);
            $lastServerFileName = key($fileNames);
            $underScorePosition = strpos($lastServerFileName, '_');// βρες τον αριθμό που περιλαμβάνεται στο όνομα του τελευταίου αρχείου μετά το _
            $filesCount = substr($lastServerFileName, $underScorePosition + 1, strpos($lastServerFileName, '.') - $underScorePosition -1);
        } else { //Αν δεν έχει ανεβάσει ακόμη αρχεία, βάλε τον αριθμό 0
            $filesCount = 0;
        }
        $lastFileNumber = $filesCount; // κράτα τον αριθμό του τελευταίου αρχείου για την περίπτωση που θα ανεβάσει επιπλέον αρχεία
        $directory = "timetables";
        $schoolCode = Auth::guard('school')->user()->code;
        $errr = 0;
        foreach($files as $file){ // Για κάθε αρχείο που ανεβάζεις
            $filesCount = 1;       
            $timetableFiles = new TimetablesFiles();
            $timetableFiles->timetable_id = $timetableId;
            try{
                $timetableFiles->save();
            } catch(\Exception $e) {
                $errr = 1;
                // dd($e->getMessage());
                Log::channel('files')->error($schoolCode." TimetableFiles Files failed to update database field files_json");
                
            }
            $fileId = $timetableFiles->id;
            $serverFileName = $schoolCode."_".$timetableId."_".$fileId."_".$filesCount.".".$file->getClientOriginalExtension();
            $timetableFiles->filenames_json = json_encode([$serverFileName => $file->getClientOriginalName()]);
            try{
                $timetableFiles->update();
            } catch(\Exception $e) {
                $errr = 1;
                Log::channel('files')->error($schoolCode." TimetableFiles Files failed to update database field files_json");
            }
            // $fileNames[$serverFileName] = $file->getClientOriginalName();//πρόσθεσε στον πίνακα το όνομα του αρχείου που θα ανεβάσεις
            
            $fileHandler = new FilesController();
            $uploaded = $fileHandler->upload_file($directory, $file, 'local', $serverFileName);
            
            if($uploaded->getStatusCode() == 500){
                Log::channel('files')->error($teacherAfm." Files failed to upload");
                return back()->with('failure', 'Αποτυχία στην υποβολή των αρχείων. Δοκιμάστε ξανά');
            }
            
            if($errr == 1){
                return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων με τα ονόματα των αρχείων. Δοκιμάστε ξανά');
            }
        }
        
        Log::channel('files')->info($schoolCode." Timetable Files successfully uploaded");
        return back()->with('success', 'Τα αρχεία ανέβηκαν επιτυχώς');
    }

    //Διαγραφή αρχείου
    public function delete_file(TimetablesFiles $timetableFile, $serverFileName){
        dd($timetableFile, $serverFileName);
        if(Auth::guard('school')->user()->id != $timetableFile->timetable->school_id){
            return back()->with('failure', 'Δεν έχετε δικαίωμα επεξεργασίας αυτής της αίτησης.');
        }
        $fileHandler = new FilesController();
        $files = json_decode($secondment->files_json, true);
        try{
            $fileHandler->delete_file('timetables', $serverFileName, 'local');
            $databaseFileName = $files[$serverFileName];
            $key = array_search($databaseFileName, $files);
            if ($key !== false) {
                unset($files[$key]);
            }
            //dd($files);
            $secondment->files_json = json_encode($files);
            $secondment->update();
        } catch(\Exception $e) {
            return back()->with('failure', 'Αποτυχία διαγραφής αρχείου.');
        }
        return back()->with('success', 'Επιτυχής διαγραφή αρχείου: "'.$databaseFileName.'"');
    }



}
