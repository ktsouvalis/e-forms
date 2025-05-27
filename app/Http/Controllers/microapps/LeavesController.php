<?php

namespace App\Http\Controllers\microapps;

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
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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

    public function import_leaves(Request $request)
    {
        $file = $request->file('leaves_file');
        
        // Validate the input file type
        $rule = [
            'leaves_file' => 'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου (Επιτρεπτός τύπος: xlsx)');
        }
        
        // Increase execution time for this request
        ini_set('max_execution_time', 300); // 5 minutes
        
        // Store the file
        $filename = "teachers_file_leaves" . Auth::id() . ".xlsx";
        $path = $request->file('leaves_file')->storeAs('files', $filename);
        
        // Truncate table before processing
        DB::statement('TRUNCATE TABLE teacher_leaves');
        
        // Load the file with ChunkReadFilter to reduce memory usage
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        
        // Configure to read only columns we need
        $spreadsheet = $reader->load("../storage/app/$path");
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Process variables
        $row = 2; // Start from row 2
        $batchSize = 50; // Process 50 rows at a time
        $totalProcessed = 0;
        $errors = 0;
        $processedBatch = [];
        
        // Get the highest row with data
        $highestRow = min($worksheet->getHighestDataRow(), 10000); // Safety limit
        
        // Process rows in batches
        while ($row <= $highestRow) {
            $rowEmpty = true;
            
            // Check if row has data
            $cellValue = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
            if ($cellValue) {
                $rowEmpty = false;
            } else {
                // Check a few more columns to be sure it's truly empty
                for ($col = 2; $col <= 5; $col++) {
                    if ($worksheet->getCellByColumnAndRow($col, $row)->getValue()) {
                        $rowEmpty = false;
                        break;
                    }
                }
            }
            
            if ($rowEmpty) {
                $row++;
                continue;
            }
            
            // Extract teacher AFM
            $rawAfm = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
            $teacherAfm = is_string($rawAfm) ? substr($rawAfm, 2, -1) : $rawAfm;
            
            // Verify teacher exists
            if (!$teacherAfm || !Teacher::where('afm', $teacherAfm)->exists()) {
                Log::channel('throwable_db')->error("update leaves afm error: " . $teacherAfm);
                $errors++;
                $row++;
                continue;
            }
            
            try {
                // Helper function to safely get cell values
                $getCellValue = function($col) use ($worksheet, $row) {
                    $value = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    return $value !== null ? $value : '';
                };
                
                // Helper function to safely convert Excel dates
                $getExcelDate = function($col) use ($worksheet, $row) {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    if (!$cell->getValue()) {
                        return null;
                    }
                    $dateTime = Date::excelToDateTimeObject($cell->getValue());
        
                    // Format to Y-m-d for database storage
                    return $dateTime->format('Y-m-d');
                    //return LeavesController::convertExcelDate($cell);
                };
                
                // Create data array with safe value extraction
                $leaveData = [
                    'afm' => $teacherAfm,
                    'leave_type' => $getCellValue(16),
                    'leave_start_date' => $getExcelDate(17),
                    'leave_days' => $getCellValue(18),
                    'am' => $getCellValue(1),
                    'sex' => $getCellValue(3),
                    'surname' => $getCellValue(4),
                    'name' => $getCellValue(5),
                    'fathers_name' => $getCellValue(6),
                    'specialty_code' => $getCellValue(7),
                    'specialty' => $getCellValue(8),
                    'directorate' => $getCellValue(12),
                    'employment_relation' => $getCellValue(14),
                    'leave_state' => $getCellValue(15),
                    'leave_protocol_number' => $getCellValue(19),
                    'leave_protocol_date' => $getExcelDate(20),
                    'leave_description' => $getCellValue(21),
                    'creator_entity_code' => is_string($getCellValue(22)) ? substr($getCellValue(22), 2, -1) : $getCellValue(22),
                    'creator_entity_name' => $getCellValue(23),
                    'creation_date' => $getExcelDate(24),
                    'submission_date' => $getExcelDate(25),
                    'approved_days' => $getCellValue(26),
                    'approved_months' => $getCellValue(27),
                    'approved_years' => $getCellValue(28),
                    'approved_protocol_number' => $getCellValue(29),
                    'approved_protocol_date' => $getExcelDate(30),
                    'approved_description' => $getCellValue(31),
                    'revoke_description' => $getCellValue(32),
                    'approving_authority_code' => $getCellValue(33),
                    'approving_authority_name' => $getCellValue(34),
                    'last_change_date' => $getExcelDate(35),
                ];
                //dd($leaveData);
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
                Log::channel('throwable_db')->error($teacherAfm . ' ' . $e->getMessage());
                $errors++;
            }
            
            $row++;
        }
        
        // Process any remaining records
        if (!empty($processedBatch)) {
            $this->saveTeacherLeavesBatch($processedBatch);
        }
        
        // Free memory
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        gc_collect_cycles();
        
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
                $keys = [
                    'afm' => $leaveData['afm'],
                    'leave_type' => $leaveData['leave_type'],
                    //'leave_start_date' => $this->convertExcelDate($leaveData['leave_start_date']),
                    'leave_start_date' => $leaveData['leave_start_date'],
                    'leave_days' => $leaveData['leave_days'],
                ];
                
                // Remove key fields from the data array
                $data = $leaveData;
                unset($data['afm'], $data['leave_type'], $data['leave_start_date'], $data['leave_days']);
                
                TeacherLeaves::updateOrCreate($keys, $data);
            }
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::channel('throwable_db')->error('Batch save error: ' . $e->getMessage());
            throw $e; // Re-throw to be caught by the caller
        }
    }

    public static function convertExcelDate($dateCell){
        if (Date::isDateTime($dateCell)) {
            $dateValue = Date::excelToDateTimeObject($dateCell->getValue());
            $formattedDate = $dateValue->format('Y-m-d');
        }
        else{
            $formattedDate = null;
        }
        return $formattedDate;
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
            //dd('after sendLeaveToProtocol');
            if($protocol_message == false){
                return back()->with('failure', 'Aπέτυχε η αποστολή στο πρωτόκολλο. Παρακαλούμε για την αποστολή mail στο it@dipe.ach.sch.gr.');
            }
        } catch(\Exception $e) {
            print_r($e->getMessage());
            dd('stop');
            return back()->with('failure', 'Αποτυχία αποστολής αίτησης στο Πρωτόκολλο της Διεύθυνσης. Παρακαλούμε επικοινωνήστε με το Τμήμα Πληροφορικής στο it@dipe.ach.sch.gr.');
        }
        try{
            $protocol_message = explode(" - ", $protocol_message);
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
        
        // Find leave type from lookup table
        $leaveType = \App\Models\LeaveType::where('description', $leave->leave_type)->first();
        $leaveProtocolDate = Carbon::createFromFormat('Y-m-d', $leave->leave_protocol_date)->format('d/m/Y');
        $schoolProtocol = $leave->leave_protocol_number .'-'. $leaveProtocolDate;
        //dd($leave->leave_type, $leaveType);
        if(!$leaveType){
            return back()->with('failure', 'Δε βρέθηκε ο τύπος της άδειας. Παρακαλούμε επικοινωνήστε με το Τμήμα Πληροφορικής στο it@dipe.ach.sch.gr');
        }
        //dd($leave);
        $data = [
            ['name' => 'Afm', 'contents' => $leave->afm ],
            ['name' => 'SchoolCode', 'contents' => $leave->creator_entity_code ],
            ['name' => 'LeaveType', 'contents' => $leaveType->eProtocolId ],
            ['name' => 'StartDate', 'contents' => $leave->leave_start_date ],
            ['name' => 'Days', 'contents' => $leave->leave_days ],
            ['name' => 'SchoolProtocol', 'contents' => $schoolProtocol ],
            
            // ['name' => 'LeaveState', 'contents' => $leave->leave_state],
            // ['name' => 'LeaveEndDate', 'contents' => $leave->leave_end_date],
            // ['name' => 'LeaveAm', 'contents' => $leave->am],
            // ['name' => 'LeaveSex', 'contents' => $leave],
            // ['name' => 'LeaveStartDate', 'contents' => ($leave->start_date)],
            // ['name' => 'LeaveDays', 'contents' => ($leave->days)],
            // ['name' => 'LeaveProtocolNumber', 'contents' => ($leave->leave_protocol_number)],
            // ['name' => 'LeaveComments', 'contents' => ($leave->comments)],
        ];
        //dd($data);
        if($leave->files_json){
            $fileNames = json_decode($leave->files_json, true);
            foreach($fileNames as $serverFileName => $databaseFileName){
                $data[] = [
                    'name'     => 'Files',
                    'contents' => fopen(storage_path("app/teacher_leaves/$serverFileName"), 'r'),
                ];
            }
        }
                        
        $client = new Client();
        
        //return "5184 - 2024/08/06";
        $response = $client->request('POST', env('E_DIRECTORATE').'/leaves/new', [
            'headers' => [
                'X-API-Key' => env('API_KEY'),
            ],
            'multipart' => $data,
        ]);
        
        // Get the response body
        $status = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        //dd($status, $body);
        if($status != 200){
            //dd($body);
            return false;
        } else {
            //dd($body);
            return $body;
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
}
