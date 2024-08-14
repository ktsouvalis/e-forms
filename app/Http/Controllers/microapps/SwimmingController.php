<?php

namespace App\Http\Controllers\microapps;

use Illuminate\Http\Request;
use App\Models\microapps\Swimming;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FilesController;

class SwimmingController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth')->only(['index']);
        $this->middleware('isTeacher')->only(['create','edit','revoke', 'update', 'store', 'upload_files', 'delete_file', 'download_file']);
    }

    public function index(){
       return view('microapps.swimming.index');
    }

    public function create() 
    {
        // if(Auth::guard('teacher')->user()->swimming()){
        //     $swimming = Auth::guard('teacher')->user()->swimming();
        //     return redirect(route('swimming.edit', ['swimming' => $swimming->id]));
        // }
        
        return view('microapps.swimming.create');
    }
    //Επεξεργασία, προσωρινή αποθήκευση, προεπισκόπηση και οριστική υποβολή αίτησης
    public function update(Swimming $swimming, Request $request){
        // dd($request->input('action'));
        if(Auth::guard('teacher')->user()->id != $swimming->teacher_id){
            return back()->with('failure', 'Δεν έχετε δικαίωμα επεξεργασίας αυτής της αίτησης.');
        }
        // Αποθήκευσε την αίτηση
        try{
            $swimming->update($request->input());
        } catch(\Exception $e) {
            //dd($e->getMessage());
            return back()->with('failure', 'Αποτυχία αποθήκευσης αίτησης.');
        }
    }
    
    //Επεξεργασία αίτησης
    public function edit(Swimming $swimming, Request $request){
        if(Auth::guard('teacher')->user()->id != $swimming->teacher_id){
            return back()->with('failure', 'Δεν έχετε δικαίωμα επεξεργασίας αυτής της αίτησης.');
        }
        return view('microapps.swimming.edit', ['swimming' => $swimming]);
        
    }
    //Δημιουργία πρώτης αίτησης
    public function store(Request $request)
    {
        try{
        $teacher = Auth::guard('teacher')->user();
        $swimming = Swimming::updateOrCreate(
            ['teacher_id' => $teacher->id],
            [   'mobile_phone' => $request->mobile,
                'specialty' => $request->specialty == 'on' ? 1 : 0,
                'licence' => $request->licence == 'on' ? 1 : 0,
                'studied' => $request->studied == 'on' ? 1 : 0,
                'coordinator' => $request->coordinator == 'on' ? 1 : 0,
                'teacher' => $request->teacher == 'on' ? 1 : 0,
                'comments' => $request->comments,
            ]);
            return redirect(route('swimming.create', ['swimming' => $swimming->id]))->with('success',"Επιτυχής αποθήκευση αίτησης.");
            } catch(\Exception $e) {
                dd($e);
                return back()->with('failure', 'Αποτυχία αποθήκευσης αίτησης.');
            }
    }
    
    //Διαγραφή αρχείου
    public function delete_file(Swimming $swimming, $serverFileName){
        if(Auth::guard('teacher')->user()->id != $swimming->teacher_id){
            return back()->with('failure', 'Δεν έχετε δικαίωμα επεξεργασίας αυτής της αίτησης.');
        }
        $fileHandler = new FilesController();
        $files = json_decode($swimming->files_json, true);
        try{
            $fileHandler->delete_file('swimming', $serverFileName, 'local');
            $databaseFileName = $files[$serverFileName];
            $key = array_search($databaseFileName, $files);
            if ($key !== false) {
                unset($files[$key]);
            }
            //dd($files);
            $swimming->files_json = json_encode($files);
            $swimming->update();
        } catch(\Exception $e) {
            return back()->with('failure', 'Αποτυχία διαγραφής αρχείου.');
        }
        return back()->with('success', 'Επιτυχής διαγραφή αρχείου: "'.$databaseFileName.'"');
    }
    //Ανέβασμα αρχείων
    public function upload_files(Request $request, Swimming $swimming){
        if(Auth::guard('teacher')->user()->id != $swimming->teacher_id){
            return back()->with('failure', 'Δεν έχετε δικαίωμα επεξεργασίας αυτής της αίτησης.');
        }
        $request->validate([ //Έλεγξε τον τύπο των αρχείων και το μέγεθός τους
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        $files = $request->file('files');
        $fileNames = [];
        //Βρες πόσα αρχεία έχει ήδη ανεβάσει
        if($swimming->files_json !== null){
            if(count(json_decode($swimming->files_json, true)) > 0){ // Αν έχει ανεβάσει ήδη, βρες τον αριθμό του τελευταίου αρχείου από το όνομά του
            $fileNames = json_decode($swimming->files_json, true);
            end($fileNames);
            $lastServerFileName = key($fileNames);
            $underScorePosition = strpos($lastServerFileName, '_');// βρες τον αριθμό που περιλαμβάνεται στο όνομα του τελευταίου αρχείου μετά το _
            $filesCount = substr($lastServerFileName, $underScorePosition + 1, strpos($lastServerFileName, '.') - $underScorePosition -1);
            } else { //Αν είχε ανεβάσει αρχεία αλλά τα έχει διαγράψει όλα, βάλε τον αριθμό 0
                $filesCount = 0;
            }
        } else { //Αν δεν έχει ανεβάσει ακόμη αρχεία, βάλε τον αριθμό 0
            $filesCount = 0;
        }
        $previousFilesCount = $filesCount;
        $lastFileNumber = $filesCount; // κράτα τον αριθμό του τελευταίου αρχείου για την περίπτωση που θα ανεβάσει επιπλέον αρχεία
        $directory = "swimming";
        $teacherAfm = Auth::guard('teacher')->user()->afm;
        foreach($files as $file){ // Για κάθε αρχείο που ανεβάζεις
            $filesCount++;
            $serverFileName = $teacherAfm."_".$filesCount.".".$file->getClientOriginalExtension();
            $fileNames[$serverFileName] = $file->getClientOriginalName();//πρόσθεσε στον πίνακα το όνομα του αρχείου που θα ανεβάσεις
            $fileHandler = new FilesController();
            $uploaded = $fileHandler->upload_file($directory, $file, 'local', $serverFileName);
            
            if($uploaded->getStatusCode() == 500){
                Log::channel('files')->error($teacherAfm." Files failed to upload");
                return back()->with('failure', 'Αποτυχία στην υποβολή των αρχείων. Δοκιμάστε ξανά');
            }
        }
        $swimming->files_json = json_encode($fileNames);
        try{
            $swimming->save();
        } catch(\Exception $e) {
            //dd($e->getMessage());
            for($i = $previousFilesCount + 1; $i <= $filesCount; $i++){
                $fileHandler = new FilesController();
                if(file_exists(storage_path('app/swimming/'.$teacherAfm."_".$i.".pdf"))){
                    $fileHandler->delete_file('swimming', $teacherAfm."_".$i.".pdf", 'local');
                }
                if(file_exists(storage_path('app/swimming/'.$teacherAfm."_".$i.".jpg"))){
                    $fileHandler->delete_file('swimming', $teacherAfm."_".$i.".jpg", 'local');
                }
                if(file_exists(storage_path('app/swimming/'.$teacherAfm."_".$i.".jpeg"))){
                    $fileHandler->delete_file('swimming', $teacherAfm."_".$i.".jpeg", 'local');
                }
                if(file_exists(storage_path('app/swimming/'.$teacherAfm."_".$i.".png"))){
                    $fileHandler->delete_file('swimming', $teacherAfm."_".$i.".png", 'local');
                }
            }

            Log::channel('files')->error($teacherAfm." Swimming Files failed to update database field files_json");
            return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων με τα ονόματα των αρχείων. Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($teacherAfm." Swimming Files successfully uploaded");
        //Αν είμαστε σε κατάσταση που επιτρέπεται η υποβολή επιπλέον αρχείων
        return redirect(route('swimming.create'))->with('success', 'Επιτυχής υποβολή αρχείων');
    }
    //Κατέβασμα αρχείων
    public function download_file($file, $download_file_name = null){
        $username = Auth::check() ? Auth::user()->username : Auth::guard('teacher')->user()->afm;
        $directory = "swimming";
        $fileHandler = new FilesController();
        $download = $fileHandler->download_file($directory, $file, 'local', $download_file_name);
        if($download->getStatusCode() == 500){
            Log::channel('files')->error($username." File $file failed to download");
            return back()->with('failure', 'Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($username."Swimming File $file successfully downloaded");
        return $download;
    }

    public function revoke(Swimming $swimming){
        if(Auth::guard('teacher')->user()->id != $swimming->teacher_id){
            return back()->with('failure', 'Δεν έχετε δικαίωμα επεξεργασίας αυτής της αίτησης.');
        }
        try{
            $swimming->revoked = 1;
            $swimming->save();    
        } catch(\Exception $e) {
            //dd($e->getMessage());
            return back()->with('failure', 'Αποτυχία ανάκλησης αίτησης. Δοκιμάστε ξανά.');
        }
        return redirect()->route('swimming.create')->with('success', 'Η αίτηση ανακλήθηκε επιτυχώς.');
    }

    
}
