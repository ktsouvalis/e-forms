<?php

namespace App\Http\Controllers\microapps;

use DateTime;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Teacher;
use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\microapps\TeacherLeaves;
use Illuminate\Database\QueryException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LeavesController extends Controller
{
    private $microapp;

    public function __construct(){
        $this->middleware('auth')->only(['index']);
        $this->middleware('isSchool')->only(['create', 'store', 'leaveUnlock', 'showHidden', 'hideLeave', 'unhideLeave']);
        $this->microapp = Microapp::where('url', '/leaves')->first();
    }

    public function index(){
        return view('microapps.leaves.index', ['appname' => 'leaves']);
    }

    public function create()
    {
        $school = Auth::guard('school')->user();
        $microapp = Microapp::where('url', '/leaves')->first();
        
        // Φέρνουμε τις άδειες του σχολείου που:
        // 1. ΔΕΝ είναι σε κατάσταση "Ανακλήθηκε" (από το μοντέλο School)
        // 2. ΚΑΙ είναι ορατές (is_visible = 1)
        $leavesExceptRevoked = $school->leaves()->where('is_visible', 1)->get();
        
        // Ελέγχουμε αν υπάρχουν αποκρυμμένες άδειες (is_visible = 0)
        $hasHiddenLeaves = $school->leaves()->where('is_visible', 0)->exists();
        
        return view('microapps.leaves.create', [
            'appname' => 'leaves',
            'microapp' => $microapp,
            'leaves' => $leavesExceptRevoked,
            'showHiddenLeavesLink' => $hasHiddenLeaves,
        ]);
    }

    public function import_leaves(Request $request)
    {
        $file = $request->file('leaves_file');
        
        // Validate the input file type
        $rule = [
            'leaves_file' => 'mimes:csv,txt|max:10240' // 10MB max
        ];
        
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου (Επιτρεπτός τύπος: CSV)');
        }
        
        // Increase execution time for this request
        ini_set('max_execution_time', 300); // 5 minutes
        
        // Store the file
        $filename = "teachers_file_leaves" . Auth::id() . ".csv";
        $path = $request->file('leaves_file')->storeAs('files', $filename);
        $fullPath = storage_path("app/$path");
        
        // Truncate table before processing
        //DB::statement('TRUNCATE TABLE teacher_leaves');
        // Process variables
        $batchSize = 50;
        $totalProcessed = 0;
        $errors = 0;
        $processedBatch = [];
        $rowNumber = 0;
        
        // Open CSV file with UTF-8 encoding
        if (($handle = fopen($fullPath, 'r')) === false) {
            return back()->with('failure', 'Αδυναμία ανάγνωσης αρχείου CSV');
        }
        
        // Read header row and determine number of columns
        $titles = fgetcsv($handle, 0, ';');
        $numOfColumns = count($titles);
        $rowNumber++;
               
        // Tell PHP this file is Windows-1253 encoded
        stream_filter_append($handle, 'convert.iconv.Windows-1253/UTF-8');
            
        // Process CSV rows
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            // Check if any row has unexpected number of columns
            if(count($row) != $numOfColumns + 1){ // MySchool extraction has an extra semicolon in each row except title's row
                Log::channel('throwable_db')->error("update leaves column count error at row $rowNumber: expected $numOfColumns, got ".count($row));
                $errors++;
                continue;
            }
            $rowNumber++;
            // Check for empty row
            if (empty(array_filter($row))) {
                continue;
            }
            // Extract teacher AFM (column index 1, 0-based)
            $rawAfm = isset($row[1]) ? trim($row[1]) : '';
            // Remove =" and ending " if present (Excel formula notation)
            $teacherAfm = is_string($rawAfm) ? substr($rawAfm, 2, -1) : $rawAfm;
            
            // Verify teacher exists
            if (!$teacherAfm || !Teacher::where('afm', $teacherAfm)->exists()) {
                Log::channel('throwable_db')->error("update leaves afm error at row $rowNumber: " . $teacherAfm);
                $errors++;
                continue;
            }
            
            try {
                // Helper function to safely get cell values with UTF-8 encoding
                $getValue = function($index) use ($row) {
                    if (!isset($row[$index])) {
                        return '';
                    }
                    $value = trim($row[$index]);
                    
                    // Ensure UTF-8 encoding
                    if (!mb_check_encoding($value, 'UTF-8')) {
                        // Try to convert from Windows-1253 (Greek) to UTF-8
                        $value = mb_convert_encoding($value, 'UTF-8', 'Windows-1253');
                    }
                    
                    return $value !== '' ? $value : '';
                };
                
                // Extract creator entity code (remove Excel formula notation)
                $creatorEntityCode = $getValue(21);
                $creatorEntityCodeRaw = is_string($creatorEntityCode) ? substr($creatorEntityCode, 2, -1) : $creatorEntityCode;
                
                // Create data array with safe value extraction
                $leaveData = [
                    'afm' => $teacherAfm,
                    'leave_type' => $getValue(15),
                    'leave_start_date' => $this->convertDate($getValue(16)),
                    'leave_days' => $getValue(17),
                    'am' => $getValue(0),
                    'sex' => $getValue(2),
                    'surname' => $getValue(3),
                    'name' => $getValue(4),
                    'fathers_name' => $getValue(5),
                    'specialty_code' => $getValue(6),
                    'specialty' => $getValue(7),
                    'directorate' => $getValue(11),
                    'employment_relation' => $getValue(13),
                    'leave_state' => $getValue(14),
                    'leave_protocol_number' => $getValue(18),
                    'leave_protocol_date' => $this->convertDate($getValue(19)),
                    'leave_description' => $getValue(20),
                    'creator_entity_code' => $creatorEntityCodeRaw,
                    'creator_entity_name' => $getValue(22),
                    'creation_date' => $this->convertDate($getValue(23)),
                    'submission_date' => $this->convertDate($getValue(24)),
                    'approved_days' => $getValue(25),
                    'approved_months' => $getValue(26),
                    'approved_years' => $getValue(27),
                    'approved_protocol_number' => $getValue(28),
                    'approved_protocol_date' => $this->convertDate($getValue(29)),
                    'approved_description' => mb_substr($getValue(30), 0, 191), // varchar(191) limit
                    'revoke_description' => $getValue(31),
                    'approving_authority_code' => $getValue(32),
                    'approving_authority_name' => $getValue(33),
                    'last_change_date' => $this->convertDate($getValue(34)),
                ];
                //dd('leave data: ', $leaveData); 
                // Add to batch
                $processedBatch[] = $leaveData;
                $totalProcessed++;
                
                // Process batch if reached batch size
                if (count($processedBatch) >= $batchSize) {
                    $this->saveTeacherLeavesBatch($processedBatch);
                    $processedBatch = []; // Reset batch
                    // Free up memory
                    gc_collect_cycles();
                }
                
            } catch (Throwable $e) {
                Log::channel('throwable_db')->error("Row $rowNumber - AFM: $teacherAfm - " . $e->getMessage());
                $errors++;
            }
        }
        
        // Close file handle
        fclose($handle);
        
        // Process any remaining records
        if (!empty($processedBatch)) {
            $this->saveTeacherLeavesBatch($processedBatch);
        }
        
        // Free memory
        gc_collect_cycles();
        
        try {
            $this->linkCorrectedLeavesToRevoked();
        } catch(Throwable $e) {
                Log::channel('throwable_db')->error("Error in linkCorrectedLeavesToRevoked");
                //$errors++;
            }

        if ($errors > 0) {
            return redirect(url('/teachers'))->with('warning', "Ενημέρωση αδειών εκπαιδευτικών με $errors σφάλματα που καταγράφηκαν στο log throwable_db. Επεξεργάστηκαν επιτυχώς $totalProcessed εγγραφές.");
        } else {
            return redirect(url('/teachers'))->with('success', "Επιτυχής ενημέρωση $totalProcessed αδειών εκπαιδευτικών");
        }
    }

    protected function saveTeacherLeavesBatch($batch)
    { 
        DB::beginTransaction();
        try {
            foreach ($batch as $leaveData) {
                // Ignore Απουσία
                if($leaveData['leave_type'] == 'Απουσία' || $leaveData['leave_state'] == '1-Δημιουργήθηκε') {
                    //Log::channel('throwable_db')->info("Ignoring leave for AFM: " . $leaveData['afm'] . " with type 'Απουσία' or state '1-Δημιουργήθηκε'");
                    continue;
                }
                
                // Keys on which update or create is happening
                
                
                
                // Remove key fields from the data array
                $data = $leaveData;
                
                unset($data['afm'], $data['creator_entity_code'], $data['leave_protocol_number'], $data['leave_protocol_date']);
                
                TeacherLeaves::updateOrCreate($keys, $data);  
            }
            DB::commit();
        
        } catch (Throwable $e) {
            DB::rollBack();
            Log::channel('throwable_db')->error('Batch save error: ' . $e->getMessage());
            throw $e; // Re-throw to be caught by the caller
        }
    }

    public function convertDate($csvDate){
        
        if (empty($csvDate)) {
            //dd('empty date');
            return null;
        }
        
        $date = DateTime::createFromFormat('d/m/Y', $csvDate);
        
        if ($date) {
            return $date->format('Y-m-d');
        } else {
            return null;
        }
    }

    private function linkCorrectedLeavesToRevoked() {
        $leavesToCheck = TeacherLeaves::whereIn('leave_state', ['2-Υποβλήθηκε', '3-Εγκρίθηκε'])->get();
        
        // Check if this leave (if it's state 5- Ανακλήθηκε) has a related active leave
        // Αν βρούμε την ίδια άδεια (ΑΦΜ, αριθμό πρωτοκόλλου, ημερομηνία πρωτοκόλλου) σε κατάσταση Ανακλήθηκε και σε κατάσταση Υποβλήθηκε ή Εγκρίθηκε
        // Συνδέουμε τις δύο άδειες ώστε μετά να δείξουμε στο χρήστη τη σωστή αντί της ανακλημένης
        foreach($leavesToCheck as $leave){
            $revokedLeave = TeacherLeaves::getRevokedLeaves()
                ->where('afm', $leave->afm)
                ->where('creator_entity_code', $leave->creator_entity_code)
                ->where('leave_protocol_number', $leave->leave_protocol_number)
                ->where('leave_protocol_date', $leave->leave_protocol_date)
                ->orderBy('id', 'desc')
                ->first();
            
                // If we found an active leave, set the related_leave_id
                if ($revokedLeave) {
                    $leave->related_leave_id = $revokedLeave->id;
                    $leave->submitted = 0;
                    //dd($revokedLeave);
                    $leave->save();
                }
        }
        
    }
    
    public function upload_files(Request $request, TeacherLeaves $teacher_leave){
        // Method for the school to upload files
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
        // Method that sends leave to protocol
        if(Auth::guard('school')->user()->code != $leave->creator_entity_code){
            return back()->with('failure', 'Δεν έχετε δικαίωμα υποβολής αυτής της άδειας.');
        }
        //Στείλε την αίτηση στο πρωτόκολλο
        try{
            $protocol_message = $this->sendLeaveToProtocol($leave);
            //dd('after sendLeaveToProtocol');
            if($protocol_message['success'] == false){
                return back()->with('failure', 'Aπέτυχε η αποστολή στο πρωτόκολλο με μήνυμα: ' . $protocol_message['message'] . ' Παρακαλούμε για την αποστολή mail στο it@dipe.ach.sch.gr.');
            }
        } catch(\Exception $e) {
            print($e->getMessage());
            print_r($e->getMessage());
            //dd('stop');
            return back()->with('failure', 'Αποτυχία αποστολής αίτησης στο Πρωτόκολλο της Διεύθυνσης. Παρακαλούμε επικοινωνήστε με το Τμήμα Πληροφορικής στο it@dipe.ach.sch.gr.');
        }
        try{
            $protocol_message = explode(" - ", $protocol_message['message']);
            $leave->protocol_number = $protocol_message[0];
            //$leave->protocol_date = $protocol_message[1];
            $leave->protocol_date = Carbon::createFromFormat('d/m/Y', $protocol_message[1])->format('Y-m-d');
            $leave->save();
        } catch(\Exception $e) {
            //dd($e->getMessage());
            return back()->with('failure', 'Η άδεια πρωτοκολλήθηκε με επιτυχία στο Πρωτόκολλο  της Διεύθυνσης αλλά απέτυχε η αποθήκευση του αριθμού πρωτοκόλλου. Παρακαλούμε επικοινωνήστε άμεσα με το Τμήμα Πληροφορικής στο it@dipe.ach.sch.gr.');
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
            if(empty($files)){
                $leave->files_json = null; // Αν δεν υπάρχουν άλλα αρχεία, βάλε το πεδίο σε null
            }
            else{
                $leave->files_json = json_encode($files);
            }
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
        
        // Find leave type from lookup table
        $leaveType = \App\Models\LeaveType::where('description', $leave->leave_type)->first();
        //dd($leave->leave_protocol_date);
        $leaveProtocolDate = $leave->leave_protocol_date->format('d/m/Y');;
        
        $schoolProtocol = $leave->leave_protocol_number .'-'. $leaveProtocolDate;
        //dd('reached 1');
        if(!$leaveType){
            return ['success' => false, 'message' => 'No leave type found for: ' . $leave->leave_type];
        }
        
        $data = [
            ['name' => 'Afm', 'contents' => $leave->afm ],
            ['name' => 'SchoolCode', 'contents' => $leave->creator_entity_code ],
            ['name' => 'LeaveType', 'contents' => $leaveType->eProtocolId ],
            ['name' => 'StartDate', 'contents' => $leave->leave_start_date ],
            ['name' => 'Days', 'contents' => $leave->leave_days ],
            ['name' => 'SchoolProtocol', 'contents' => $schoolProtocol ],
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

        if($leave->protocol_number && $leave->protocol_date){
            $data[] = ['name' => 'ProtocolNum', 'contents' => $leave->protocol_number];
            $data[] = ['name' => 'ProtocolYear', 'contents' => $leave->protocol_date->format('Y')];
        }
     
        $client = new Client();
        
        //return "5184 - 2024/08/06";
        //Log::channel('files')->info("before request");
        try{
           $response = $client->request('POST', env('E_DIRECTORATE').'/leaves/new', [
                'headers' => [
                    'X-API-Key' => env('API_KEY'),
                ],
                'multipart' => $data,
            ]); 
        } catch (\Exception $e) {
            
            Log::channel('files')->error("Leave ID: ".$leave->id." - Protocol Request Exception: " . $e->getMessage());
            Log::channel('files')->info("Leave ID: ".$leave->id." - Data: " . json_encode($data));
            return ['success' => false, 'message' => 'Protocol Request Exception: ' . $e->getMessage()];
        }
        
        //Log::channel('files')->info("After request");
        // Get the response body
        $status = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        Log::channel('files')->info("Leave ID: ".$leave->id." - Protocol Response Status: $status - Body: $body");
        //dd($status, $body);
        if($status != 200){
            return ['success' => false, 'message' => 'Protocol Response Status: ' . $status];
        } else {
            //dd($body);
            return ['success' => true, 'message' => $body];
        }
    }

    public function getTeacherLeaves(TeacherLeaves $teacher_leave){
        $leaves = TeacherLeaves::where('afm', $teacher_leave->afm)->
                                where('leave_state', '3-Εγκρίθηκε')->
                                where('leave_type', $teacher_leave->leave_type)->get();
        // check if teacher is director
        $teacher = Teacher::where('afm', $teacher_leave->afm)->first();
        $isDirector = $teacher->isDirector();
        // return response()->json([
        //     'isDirector' => $teacher->isDirector(),
        //     'leaves' => $leaves,
        // ]);
        return response()->json([
            'leaves' => $leaves,
            'isDirector' => $isDirector,
        ]);
    }

    public function leaveUnlock(TeacherLeaves $teacher_leave) {
        if($teacher_leave->submitted == 1){
            $teacher_leave->submitted = 0;
            try {
                $teacher_leave->save();
                return redirect()->back()->with('success', 'Η άδεια ξεκλειδώθηκε επιτυχώς και μπορείτε να την επεξεργαστείτε.');
            } catch (\Exception $e) { 
                return redirect()->back()->with('error', 'Σφάλμα κατά το ξεκλείδωμα της άδειας: ' . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('warning', 'Η άδεια δεν είναι υποβεβλημένη και δεν χρειάζεται ξεκλείδωμα.');
        }
    }

    /**
     * Εμφάνιση αποκρυμμένων αδειών για το σχολείο
     */
    public function showHidden()
    {
        $school = Auth::guard('school')->user();
        $microapp = Microapp::where('url', '/leaves')->first();
        
        // Φέρνουμε τις αποκρυμμένες άδειες του σχολείου
        // (εκτός από τις ανακληθείσες)
        $hiddenLeaves = $school->leaves()
            ->where('is_visible', 0)
            ->orderBy('leave_start_date', 'desc')
            ->get();
        
        return view('microapps.leaves.hidden', [
            'appname' => 'leaves',
            'microapp' => $microapp,
            'hiddenLeaves' => $hiddenLeaves,
        ]);
    }

    /**
     * Απόκρυψη άδειας
     */
    public function hideLeave($teacher_leave)
    {
        $school = Auth::guard('school')->user();
        $leave = TeacherLeaves::findOrFail($teacher_leave);
        
        // Έλεγχος ότι η άδεια ανήκει στο σχολείο
        // Προσάρμοσε το πεδίο ανάλογα με το πώς συνδέεται η άδεια με το σχολείο
        if ($leave->creator_entity_code !== $school->code) {
            abort(403, 'Unauthorized action.');
        }
        
        // Έλεγχος: Μπορεί να αποκρυφθεί μόνο αν:
        // 1. submitted = 0 ΚΑΙ protocol_number = null
        // 2. submitted = 1 ΚΑΙ protocol_number υπάρχει
        $canHide = false;
        
        if (!$leave->submitted && !$leave->protocol_number) {
            $canHide = true;
        } elseif ($leave->submitted && $leave->protocol_number) {
            $canHide = true;
        }
        
        if (!$canHide) {
            return redirect()->back()->with('error', 'Δεν μπορείτε να αποκρύψετε αυτή την άδεια σε αυτή την κατάσταση.');
        }
        
        // Έλεγχος ότι δεν είναι ανακληθείσα
        if ($leave->leave_state === 'Ανακλήθηκε') {
            return redirect()->back()->with('error', 'Δεν μπορείτε να αποκρύψετε μια ανακληθείσα άδεια.');
        }
        
        // Ενημέρωση της άδειας
        $leave->is_visible = 0;
        $leave->save();
        
        return redirect()->back()->with('success', 'Η άδεια αποκρύφθηκε επιτυχώς.');
    }

    /**
     * Επαναφορά ορατότητας άδειας
     */
    public function unhideLeave($teacher_leave)
    {
        $school = Auth::guard('school')->user();
        $leave = TeacherLeaves::findOrFail($teacher_leave);
        
        // Έλεγχος ότι η άδεια ανήκει στο σχολείο
        if ($leave->creator_entity_code !== $school->code) {
            abort(403, 'Unauthorized action.');
        }
        
        // Επαναφορά της ορατότητας
        $leave->is_visible = 1;
        $leave->save();
        
        return redirect()->route('leaves.create')->with('success', 'Η άδεια εμφανίζεται πάλι στη λίστα.');
    }

    
}
