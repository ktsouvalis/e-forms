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
        $this->middleware('auth')->only(['index', 'change_status', 'comment']);
        $this->middleware('isSchool')->only(['create','upload_files', 'upload_file', 'delete_file'])->except(['index']);
        $this->microapp = Microapp::where('url', '/timetables')->first();
    }

    public function index(){
       // return view('microapps.enrollments.for_niki', ['appname' => 'enrollments']);
        return view('microapps.timetables.index', ['appname' => 'timetables']);
    }

    public function create(){
        return view('microapps.timetables.create', ['appname' => 'timetables']);
    }

    public function edit(Timetables $timetable){
        return view('microapps.timetables.edit', ['appname' => 'timetables', 'timetable' => $timetable]);
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
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx|max:2048',
        ]);
        $files = $request->file('files');
        $fileNames = [];
        //Αν δεν υπάρχει, δημιούργησε ένα νέο Ωρολόγιο Πρόγραμμα
        if(!$timetable){
            $timetable = new Timetables();
            $timetable->school_id = Auth::guard('school')->user()->id;
            $timetable->status = 0;
            try{
                $timetable->save();
            } catch(\Exception $e) {
                Log::channel('files')->error($schoolCode." Timetable failed to update database field files_json");
                return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων. Δοκιμάστε ξανά');
            }
        }
        //Βρες πόσα αρχεία έχει ήδη ανεβάσει γι αυτό το πρόγραμμα
        $timetableId = $timetable->id;
        $timetableFiles = TimetablesFiles::where('timetable_id', $timetableId)->get();
        $directory = "timetables";
        $schoolCode = Auth::guard('school')->user()->code;
        $errr = 0;
        foreach($files as $file){ // Για κάθε αρχείο που ανεβάζεις
            $filesCount = 1;//Αρχικοποίησε τον μετρητή των αρχείων - τα αρχεία που ανεβαίνουν από αυτή τη Φόρμα έχουν πάντα αριθμό 1   
            $timetableFiles = new TimetablesFiles();
            $timetableFiles->timetable_id = $timetableId;
            try{
                $timetableFiles->save();
            } catch(\Exception $e) {
                $errr = 1;
                // dd($e->getMessage());
                Log::channel('files')->error($schoolCode." TimetableFiles Files failed to update database field files_json"); 
                return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων με τα ονόματα των αρχείων. Δοκιμάστε ξανά');
            }
            $fileId = $timetableFiles->id;
            $serverFileName = $schoolCode."_".$timetableId."_".$fileId."_".$filesCount.".".$file->getClientOriginalExtension();
            $timetableFiles->filenames_json = json_encode([$serverFileName => $file->getClientOriginalName()]);
            try{
                $timetableFiles->update();
            } catch(\Exception $e) {
                $errr = 1;
                Log::channel('files')->error($schoolCode." TimetableFiles Files failed to update database field files_json");
                return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων με τα ονόματα των αρχείων. Δοκιμάστε ξανά');
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
        //Ανανέωσε τον πίνακα stakeholders ώστε να φαίνεται ότι έχει υποβληθεί απάντηση στη μικροεφαρμογή
        if($this->microapp->stakeholders->count()){
            $stakeholder = $this->microapp->stakeholders->where('stakeholder_id', Auth::guard('school')->user()->id)->where('stakeholder_type', 'App\Models\School')->first();
            $stakeholder->hasAnswer = 1;
            $stakeholder->save();
        }
        Log::channel('files')->info($schoolCode." Timetable Files successfully uploaded");
        return back()->with('success', 'Τα αρχεία ανέβηκαν επιτυχώς');
    }

    public function upload_file(Request $request, $timetableFileId){
        $file = $request->file('file');
        $timetableFile = TimetablesFiles::find($timetableFileId);
        $timetableId = $timetableFile->timetable_id;
        $schoolCode = Auth::guard('school')->user()->code;
        $directory = "timetables";
        $existingFilesCount = count(json_decode($timetableFile->filenames_json, true));
        $fileCount = $existingFilesCount + 1;
        if($timetableFile->status == 2){
            $fileCount = $existingFilesCount;
        }
        $serverFileName = $schoolCode."_".$timetableId."_".$timetableFileId."_".$fileCount.".".$file->getClientOriginalExtension();
        $totalFiles = json_decode($timetableFile->filenames_json, true);
        if($timetableFile->status == 2){
            array_pop($totalFiles);
        }
        $totalFiles[$serverFileName] = $file->getClientOriginalName();
        $fileHandler = new FilesController();
        $file = $fileHandler->upload_file($directory, $file, 'local', $serverFileName);
        if($file->getStatusCode() == 500){
            Log::channel('files')->error($schoolCode." File $serverFileName failed to upload");
            return back()->with('failure', 'Αποτυχία υποβολής αρχείου. Δοκιμάστε ξανά');
        }
        $timetableFile->filenames_json = json_encode($totalFiles);
        $timetableFile->status = 2;
        try{
            $timetableFile->update();
        } catch(\Exception $e) {
            Log::channel('files')->error($schoolCode." File $serverFileName failed to update database field filenames_json");
            return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων με τα ονόματα των αρχείων. Δοκιμάστε ξανά');
        }
    
        Log::channel('files')->info($schoolCode." File $serverFileName successfully uploaded");
        return back()->with('success', 'Το αρχείο ανέβηκε επιτυχώς');
    }
    //Διαγραφή αρχείου
    public function delete_file($timetableFileId, $serverFileName){
        $timetableFile = TimetablesFiles::find($timetableFileId);
        $fileHandler = new FilesController();
        $files = json_decode($timetableFile->filenames_json, true);
        
        try{
            $fileHandler->delete_file('timetables', $serverFileName, 'local');
            if(count($files) == 1){
                $timetableFile->delete();
            } else {
                unset($files[$serverFileName]);
                $timetableFile->filenames_json = json_encode($files);
                if($timetableFile->status == 2){
                    $timetableFile->status = 1;
                }
                $timetableFile->update();
            }
            
        } catch(\Exception $e) {
            return back()->with('failure', 'Αποτυχία διαγραφής αρχείου.');
        }
        return back()->with('success', 'Επιτυχής διαγραφή αρχείου: ');
    }
    //Κατέβασμα αρχείου
    public function download_file($serverFileName, $databaseFileName = null){
        //missing middleware check if user/administrator is allowed to download file
        $directory = "timetables";
        $fileHandler = new FilesController();
        $download = $fileHandler->download_file($directory, $serverFileName, 'local', $databaseFileName);
        if($download->getStatusCode() == 500){
            return back()->with('failure', 'Δοκιμάστε ξανά');
        }
        return $download;
    }

    //Αλλαγή κατάστασης Αρχείου
    public function change_status(Request $request, TimetablesFiles $timetableFile){
        
        // //εξέτασε το ενδεχόμενο να είναι κλειδωμένο το πρόγραμμα και να πρέπει να ανοίξει
        // if($request->status == 2){ // αν πάει να το βάλει σε αναμονή διορθώσεων
        //     $timetable = $timetableFile->timetable;
        //     if($timetable->status == 1){ // και αν το πρόγραμμα είναι οριστικοποιημένο
        //         $otherTimetable = Timetables::where('school_id', $timetable->school->id)                         // δες αν υπάρχει άλλο ανοικτό πρόγραμμα γι αυτό το σχολείο
        //                                     ->where('status', 0)
        //                                     ->first();
        //         Log::channel('files')->info($otherTimetable." Timetable Files successfully uploaded");
        //         if($otherTimetable->count() > 0){ // αν υπάρχει
        //             return back()->with('failure', 'Δεν μπορείτε να αναιρέσετε την οριστική υποβολή ενός προγράμματος ενώ υπάρχει ένα άλλο ανοικτό');
        //         }
        //         $timetable->status = 0; //Αν περάσει, άνοιξε το πρόγραμμα
        //         try{
        //             $timetable->update();
        //         } catch(\Exception $e) {
        //             return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων. Δοκιμάστε ξανά');
        //         }
        //     }
        // }
        $timetableFile->status = $request->status;
        try{
            $timetableFile->update();
        } catch(\Exception $e) {
            return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων. Δοκιμάστε ξανά');
        }
        //Αν έχουν εγκριθεί όλα τα αρχεία οριστικοποίησε το πρόγραμμα
        $timetableFiles = TimetablesFiles::where('timetable_id', $timetableFile->timetable_id)->get();
        $lockTimetable = 1;
        foreach($timetableFiles as $file){
            if($file->status != 3){
                $lockTimetable = 0;
            }
        }
        if($lockTimetable){
            $timetable = Timetables::find($timetableFile->timetable_id);
            $timetable->status = 1;
            try{
                $timetable->update();
            } catch(\Exception $e) {
                return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων. Δοκιμάστε ξανά');
            }
        }
        return back()->with('success', 'Επιτυχής αλλαγή κατάστασης αρχείου');
    }

    //Σχολιασμός Αρχείου
    public function comment(Request $request, TimetablesFiles $timetableFile, $thisCount){
        // dd($request->all(), $timetableFile);
        $data = [
            'thisCount' => $thisCount,
            'comments' => $request->comments
        ];
        $timetableFile->comments = json_encode($data);
        try{
            $timetableFile->update();
        } catch(\Exception $e) {
            dd($e->getMessage());
            return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων. Δοκιμάστε ξανά');
        }
        return back()->with('success', 'Επιτυχής σχολιασμός αρχείου');
    }



}
