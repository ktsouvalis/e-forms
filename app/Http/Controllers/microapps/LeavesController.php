<?php

namespace App\Http\Controllers\microapps;

use GuzzleHttp\Client;
use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\microapps\TeacherLeaves;
use App\Http\Controllers\FilesController;

class LeavesController extends Controller
{
    private $microapp;

    public function __construct(){
        $this->middleware('auth')->only(['index']);
        $this->middleware('isSchool')->only(['create', 'store']);
        $this->microapp = Microapp::where('url', '/leaves')->first();
    }

    public function index(){
        return view('microapps.leaves.index', ['appname' => 'leaves']);
    }

    public function create(){
        return view('microapps.leaves.create', ['appname' => 'leaves']);
    }

    public function upload_files(Request $request, TeacherLeaves $teacher_leave){
        if(Auth::guard('school')->user()->code != $teacher_leave->creator_entity_code){
            return back()->with('failure', 'Δεν έχετε δικαίωμα επεξεργασίας αυτής της άδειας.');
        }
        $request->validate([ //Έλεγξε τον τύπο των αρχείων και το μέγεθός τους
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        $files = $request->file('files');
        
        $fileNames = [];
        //Βρες πόσα αρχεία έχει ήδη ανεβάσει
        if($teacher_leave->files_json){ // Αν έχει ανεβάσει ήδη, βρες τον αριθμό του τελευταίου αρχείου από το όνομά του
            $fileNames = json_decode($teacher_leave->files_json, true);
            end($fileNames);
            $lastServerFileName = key($fileNames);
            $underScorePosition = strpos($lastServerFileName, '_');// βρες τον αριθμό που περιλαμβάνεται στο όνομα του τελευταίου αρχείου μετά το _
            $filesCount = substr($lastServerFileName, $underScorePosition + 1, strpos($lastServerFileName, '.') - $underScorePosition -1);
        } else { //Αν δεν έχει ανεβάσει ακόμη αρχεία, βάλε τον αριθμό 0
            $filesCount = 0;
        }
        $lastFileNumber = $filesCount; // κράτα τον αριθμό του τελευταίου αρχείου για την περίπτωση που θα ανεβάσει επιπλέον αρχεία
        $directory = "teacher_leaves";
        foreach($files as $file){ // Για κάθε αρχείο που ανεβάζεις
            $filesCount++;
            $serverFileName = $teacher_leave->id."_".$filesCount.".".$file->getClientOriginalExtension();
            $fileNames[$serverFileName] = $file->getClientOriginalName();//πρόσθεσε στον πίνακα το όνομα του αρχείου που θα ανεβάσεις
            $fileHandler = new FilesController();
            $uploaded = $fileHandler->upload_file($directory, $file, 'local', $serverFileName);
            
            if($uploaded->getStatusCode() == 500){
                Log::channel('files')->error($teacherAfm." Files failed to upload");
                return back()->with('failure', 'Αποτυχία στην υποβολή των αρχείων. Δοκιμάστε ξανά');
            }
        }
        $teacher_leave->files_json = json_encode($fileNames);
        try{
            $teacher_leave->save();
        } catch(\Exception $e) {
            //dd($e->getMessage());
            Log::channel('files')->error($teacher_leave->id." Teacher Leave Files failed to update database field files_json");
            return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων με τα ονόματα των αρχείων. Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($teacher_leave->id." Teacher Leave Files successfully uploaded");
        // dd($teacher_leave, $request->all());
        return redirect(url('leaves/create'))->with('success','Τα αρχεία ανέβηκαν.');//response()->json(['message' => 'Τα αρχεία ανέβηκαν επιτυχώς (fake)!']);
    }

    public function submit(TeacherLeaves $leave){
        if(Auth::guard('school')->user()->code != $leave->creator_entity_code){
            return back()->with('failure', 'Δεν έχετε δικαίωμα υποβολής αυτής της άδειας.');
        }
        //Στείλε την αίτηση στο πρωτόκολλο
        try{
            $protocol_message = $this->sendLeaveToProtocol($leave);
            if($protocol_message == false){
                return back()->with('failure', 'Aπέτυχε η αποστολή στο πρωτόκολλο. Παρακαλούμε για την αποστολή mail στο it@dipe.ach.sch.gr.');
            }
            $protocol_message = explode(" - ", $protocol_message);
            $leave->protocol_number = $protocol_message[0];
            $leave->protocol_date = $protocol_message[1];
            $leave->save();
        } catch(\Exception $e) {
            // dd($e->getMessage());
            return back()->with('failure', 'Αποτυχία αποστολής αίτησης στο Πρωτόκολλο της Διεύθυνσης. Παρακαλούμε επικοινωνήστε με το Τμήμα Πληροφορικής στο it@dipe.ach.sch.gr.');
        }
        //Οριστικοποίησε την αίτηση - criteria_submitted = 1
        try{
            $leave->submitted = 1;
            $leave->save();
        } catch(\Exception $e) {
            //dd($e->getMessage());
            return back()->with('failure', 'Η άδεια πρωτοκολλήθηκε με επιτυχία στο Πρωτόκολλο  της Διεύθυνσης αλλά απέτυχε η οριστικοποίησή της. Παρακαλούμε επικοινωνήστε άμεσα με το Τμήμα Πληροφορικής στο  it@dipe.ach.sch.gr.');
        }
        return redirect(url('leaves/create'))->with('success',"Η άδεια υποβλήθηκε στο Πρωτόκολλο της ΔΙΠΕ Αχαΐας με αρ. πρωτ. $protocol_message[0] - $protocol_message[1].");
    }

    public function download_file($file, $download_file_name = null){
        $username = Auth::check() ? Auth::user()->username : Auth::guard('school')->user()->code;
        $directory = "teacher_leaves";
        $fileHandler = new FilesController();
        $download = $fileHandler->download_file($directory, $file, 'local', $download_file_name);
        if($download->getStatusCode() == 500){
            Log::channel('files')->error($username." File $file failed to download");
            return back()->with('failure', 'Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($username." File $file successfully downloaded");
        return $download;
    }

    public function delete_file($serverFileName, $leaveId){
        $leave = TeacherLeaves::findOrFail($leaveId);
        if(Auth::guard('school')->user()->code != $leave->creator_entity_code){
            return back()->with('failure', 'Δεν έχετε δικαίωμα επεξεργασίας αυτής της αίτησης.');
        }
        $fileHandler = new FilesController();
        $files = json_decode($leave->files_json, true);
        try{
            $fileHandler->delete_file('teacher_leaves', $serverFileName, 'local');
            $databaseFileName = $files[$serverFileName];
            $key = array_search($databaseFileName, $files);
            if ($key !== false) {
                unset($files[$key]);
            }
        
            $leave->files_json = json_encode($files);
            $leave->update();
        } catch(\Exception $e) {
            //dd($e->getMessage());
            Log::channel('files')->info("Teacher Leave File DatabaseFileName: $databaseFileName, ServerFileName: $serverFileName failed to delete");
            return back()->with('failure', 'Αποτυχία διαγραφής αρχείου.');
        }
        Log::channel('files')->info("Teacher Leave File DatabaseFileName: $databaseFileName, ServerFileName: $serverFileName deleted succesfully.");
        return back()->with('success', 'Επιτυχής διαγραφή αρχείου: "'.$databaseFileName.'"');
    }

    public function sendLeaveToProtocol(TeacherLeaves $leave){
        $data = [
            ['name' => 'Afm', 'contents' => $leave->afm ],
            ['name' => 'LeaveType', 'contents' => ($leave->leave_type)],
            ['name' => 'LeaveStartDate', 'contents' => ($leave->start_date)],
            ['name' => 'LeaveDays', 'contents' => ($leave->days)],
            ['name' => 'LeaveProtocolNumber', 'contents' => ($leave->leave_protocol_number)],
            ['name' => 'LeaveComments', 'contents' => ($leave->comments)],
        ];
        if($leave->files_json){
            $fileNames = json_decode($leave->files_json, true);
            foreach($fileNames as $serverFileName => $databaseFileName){
                $data[] = [
                    'name'     => 'Files',
                    'contents' => fopen(storage_path("app/teacher_leaves/$serverFileName"), 'r'),
                ];
            }
        }
                                
        // if($secondment->teacher->work_experience){
        //     $data[] = ['name' => 'WorkExperienceYears', 'contents' => $secondment->teacher->work_experience->years];
        //     $data[] = ['name' => 'WorkExperienceMonths', 'contents' => $secondment->teacher->work_experience->months];
        //     $data[] = ['name' => 'WorkExperienceDays', 'contents' => $secondment->teacher->work_experience->days];
        // }
        $client = new Client();
        return "5184 - 2024/08/06";
        $response = $client->request('POST', env('E_DIRECTORATE').'/application/leaves', [
            'headers' => [
                'X-API-Key' => env('API_KEY'),
            ],
            'multipart' => $data,
        ]);
        // Get the response body
        $status = $response->getStatusCode();
        $body = $response->getBody();
        
        if($status != 200){
            //dd($body);
            return false;
        } else {
            return $body;
        }
    }
}
