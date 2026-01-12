<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Month;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Events\SchoolsTeachersUpdated;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class SchoolController extends Controller
{
    /**
 * Import and process schools from CSV file
 *
 * @param Request $request The HTTP request object.
 * @return \Illuminate\Http\RedirectResponse The redirect response.
 */
public function importSchools(Request $request)
{
    // Validate the user's input
    $rule = [
        'import_schools' => 'required|mimetypes:text/csv,text/plain,application/csv'
    ];
    $validator = Validator::make($request->all(), $rule);
    
    if ($validator->fails()) { 
        return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου (Επιτρεπτός τύπος: csv)');
    }

    // Store the file temporarily
    $filename = "schools_file" . Auth::id() . ".csv";
    $path = $request->file('import_schools')->storeAs('files', $filename);
    $fullPath = storage_path("app/$path");

    $error = false;
    $wasChanged = false;
    $processedCount = 0;
    $errorCount = 0;

    try {
        // Open and read CSV file
        if (($handle = fopen($fullPath, "r")) === false) {
            throw new \Exception("Unable to open CSV file");
        }

        // Skip header rows (assuming first 2 rows are headers)
        fgetcsv($handle);
        //fgetcsv($handle);
        // Tell PHP this file is Windows-1253 encoded
        stream_filter_append($handle, 'convert.iconv.Windows-1253/UTF-8');
        
        // Process each row
        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            //dd($data[6]);
            // Skip empty rows
            if (empty(array_filter($data))) {
                continue;
            }

            $processedCount++;
            
            try {
                // Extract data from CSV columns (adjust indices based on your CSV structure)
                $schoolData = $this->extractSchoolDataFromCsv($data);
                
                // Validate municipality exists
                if (!Municipality::where('name', $schoolData['municipality_name'])->exists()) {
                    $errorCount++;
                    Auth::user()->notify(new UserNotification(
                        "Στη γραμμή " . ($processedCount + 2) . " o Δήμος {$schoolData['municipality_name']} δεν υπάρχει στη βάση δεδομένων",
                        'Σφάλμα: Άγνωστος Δήμος ' . $schoolData['municipality_name']
                    ));
                    continue;
                }

                $municipalityId = Municipality::where('name', $schoolData['municipality_name'])->first()->id;
    
                // Create or update school
                $schoolModel = School::updateOrCreate(
                    ['code' => $schoolData['code']],
                    [
                        'name' => str_replace('/', '', $schoolData['name']),
                        'code' => $schoolData['code'],
                        'municipality_id' => $municipalityId,
                        'primary' => $schoolData['primary'],
                        'leitourgikotita' => $schoolData['leitourgikotita'],
                        'organikotita' => $schoolData['organikotita'],
                        'telephone' => $schoolData['telephone'],
                        'is_active' => $schoolData['is_active'],
                        'has_all_day' => $schoolData['has_all_day'],
                        'md5' => md5($schoolData['code']),
                        'mail' => $schoolData['mail'],
                        'experimental' => $schoolData['experimental'],
                        'special_needs' => $schoolData['special_needs'],
                        'public' => $schoolData['public'],
                        'has_integration_section' => $schoolData['has_integration_section'],
                        'address' => $schoolData['address'],
                    ]
                );

                if ($schoolModel->wasRecentlyCreated || $schoolModel->wasChanged()) {
                    $wasChanged = true;
                }

            } catch (\Throwable $e) {
                $error = true;
                $errorCount++;
                Log::channel('throwable_db')->error(
                    Auth::user()->username . ' create school error ' . 
                    ($schoolData['code'] ?? 'unknown') . ' ' . $e->getMessage()
                );
            }
        }

        fclose($handle);

        // Clean up temporary file
        Storage::delete($path);

        // Update last modified timestamp if changes were made
        if ($wasChanged) {
            DB::table('last_update_schools')->updateOrInsert(
                ['id' => 1],
                ['date_updated' => now()]
            );
            event(new SchoolsTeachersUpdated());
        }

        // Return appropriate response
        if (!$error) {
            if ($wasChanged) {
                Log::channel('user_memorable_actions')->info(Auth::user()->username . ' importSchools');
                return redirect(url('/schools'))
                    ->with('success', "Η εισαγωγή ολοκληρώθηκε. Επεξεργάστηκαν $processedCount εγγραφές.");
            } else {
                Log::channel('user_memorable_actions')->info(Auth::user()->username . ' importSchools with no change.');
                return redirect(url('/schools'))
                    ->with('success', 'Δεν υπήρχε καμία μεταβολή στα στοιχεία των Σχολείων.');
            }
        } else {
            Log::channel('user_memorable_actions')->warning(Auth::user()->username . ' importSchools with errors');
            return redirect(url('/schools'))
                ->with('warning', "Η εισαγωγή ολοκληρώθηκε με $errorCount σφάλματα που καταγράφηκαν στο log throwable_db");
        }

    } catch (\Throwable $e) {
        // Clean up file on error
        if (Storage::exists($path)) {
            Storage::delete($path);
        }

        Log::channel('throwable_db')->error(
            Auth::user()->username . ' importSchools fatal error: ' . $e->getMessage()
        );
        
        return back()->with('failure', 'Υπήρξε σφάλμα κατά την επεξεργασία του αρχείου');
    }
}

/**
 * Extract school data from CSV row
 *
 * @param array $data CSV row data
 * @return array Processed school data
 */
private function extractSchoolDataFromCsv(array $data): array
{
    //dd($data);
    // Handle code field (remove formula if present)
    $code = $data[12] ?? '';
    if (str_contains($code, "=")) {
        $code = substr($code, 2, -1);
    }

    // Extract school type from column 11
    $schoolType = $data[11] ?? '';
    if($code == '7061041'){
        //dd($data[49]);
    }
    return [
        'name' => $data[13] ?? '',
        'code' => $code,
        'municipality_name' => $data[6] ?? '',
        'primary' => str_contains($schoolType, "Δημοτικό Σχολείο") ? 1 : 0,
        'leitourgikotita' => !empty($data[14]) ? $data[14] : 0,
        'organikotita' => !empty($data[15]) ? $data[15] : 0,
        'telephone' => !empty($data[17]) ? $data[17] : '-',
        'is_active' => ($data[49] ?? '') == "NAI" ? 0 : 1, // Invert logic: True means inactive
        'has_all_day' => ($data[50] ?? '') == "NAI" ? 0 : 1, // Invert logic: True means all-day suspended
        'mail' => !empty($data[19]) ? $data[19] : '-',
        'address' => !empty($data[21]) ? $data[21] : '-',
        'has_integration_section' => ($data[33] ?? '') == "NAI" ? 1 : 0,
        'special_needs' => str_contains($schoolType, "Ειδικής Αγωγής") ? 1 : 0,
        'experimental' => str_contains($schoolType, "Πειραματικό") ? 1 : 0,
        'public' => !str_contains($data[10] ?? '', "Ιδιωτικά Σχολεία") ? 1 : 0,
    ];
    
}
    //
    public function login($md5){ 
        
        return redirect(url('/'))->with('warning', "Η σύνδεση γίνεται πλέον με τους κωδικούς του σχολείου στο ΠΣΔ ή στο Myschool. Πατήστε 'Σύνδεση Σχολείου / Εκπαιδευτικού' για να συνδεθείτε.");
        
        // $school = School::where('md5', $md5)->firstOrFail();
        // Auth::guard('school')->login($school);
        // $school->logged_in_at = Carbon::now();
        // $school->save();
        // session()->regenerate();

        // return redirect(url('/index_school'))->with('success', "$school->name καλωσήρθατε");
    }

    public function logout(){
        auth()->guard('school')->logout();
        return redirect(url('/'))->with('success', 'Αποσυνδεθήκατε');
    }

    public function importDirectors(Request $request){
    $rule = [
        'directors_file' => 'required|mimetypes:text/csv,text/plain,application/csv',
        'subdirectors_file' => 'required|mimetypes:text/csv,text/plain,application/csv'
    ];
    $validator = Validator::make($request->all(), $rule);
    if($validator->fails()){ 
        return back()->with('failure', 'Παρακαλώ υποβάλετε δύο αρχεία .csv');
    }
    
    //store the files
    $filename_directors = "directors_file".Auth::id().".csv";
    $path_directors = $request->file('directors_file')->storeAs('files', $filename_directors);
    $filename_subdirectors = "subdirectors_file".Auth::id().".csv";
    $path_subdirectors = $request->file('subdirectors_file')->storeAs('files', $filename_subdirectors);

    $directors_array = array();
    $subdirectors_array = array();
    $error = 0;

    // Read director's File
    if (($handle = fopen("../storage/app/$path_directors", "r")) !== FALSE) {
        // Skip header row
        fgetcsv($handle, 0, ";");
        
        $row = 2; // Start from row 2 (after header)
        // Tell PHP this file is Windows-1253 encoded
        stream_filter_append($handle, 'convert.iconv.Windows-1253/UTF-8');
        
        while (($data = fgetcsv($handle, 0, ";")) !== FALSE && $row < 10000) {
            // Check if row is empty
            if (empty(array_filter($data))) {
                break;
            }
            
            $check = array();
            
            // Get code from column 8 (index 7)
            $code = $data[7] ?? null;
            // Get AFM from column 16 (index 15)
            $afm = $data[15] ?? null;
            // Get deputy_director from column 33 (index 32)
            $check['deputy_director'] = $data[32] ?? null;
            // Get public_school from column 6 (index 5)
            $check['public_school'] = $data[5] ?? null;
            
            $check['school_name'] = '';
            $check['director_surname'] = '';
            
            if ($code && str_contains($code, "=")) {
                $check['code'] = substr($code, 2, -1);
            } else {
                $check['code'] = $code;
            }
            
            if ($afm && str_contains($afm, "=")) {
                $check['afm'] = substr($afm, 2, -1);
            } else {
                $check['afm'] = $afm;
            }
            
            if (!School::where('code', $check['code'])->count()) {
                $check['code'] = 'Άγνωστος κωδικός σχολείου';
                $error = 1;
                Auth::user()->notify(new UserNotification("Στη γραμμή $row ο κωδικός σχολείου ".$check['code']." δεν υπάρχει στη βάση δεδομένων", 'Σφάλμα ενημέρωσης διευθυντών: Κωδικός Σχολείου '. $check['code']));
            } else {
                $school = School::where('code', $check['code'])->first();
                $check['school_name'] = $school->name;
                $check['school_id'] = $school->id;
            }
            
            if (!Teacher::where('afm', $check['afm'])->count()) {
                $check['afm'] = 'Άγνωστος ΑΦΜ Εκπαιδευτικού';
                $error = 1;
                Auth::user()->notify(new UserNotification("Στη γραμμή $row ο ΑΦΜ ".$check['afm']." δεν υπάρχει στη βάση δεδομένων", 'Σφάλμα ενημέρωσης διευθυντών: Κωδικός Σχολείου '. $check['code']));
            } else {
                $teacher = Teacher::where('afm', $check['afm'])->first();
                $check['teacher_id'] = $teacher->id;
                $check['director_surname'] = $teacher->surname;
            }

            // Prepare directors array to pass it in session
            array_push($directors_array, $check);
            $row++;
        }
        fclose($handle);
    }
    
    $directors_array = $this->removeDuplicateDeputyDirectors($directors_array);
    
    // Read subdirector's File
    if (($handle = fopen("../storage/app/$path_subdirectors", "r")) !== FALSE) {
        // Skip header row
        fgetcsv($handle, 0, ";");
        
        $row = 2;
        // Tell PHP this file is Windows-1253 encoded
        stream_filter_append($handle, 'convert.iconv.Windows-1253/UTF-8');
        while (($data = fgetcsv($handle, 0, ";")) !== FALSE && $row < 10000) {
            // Check if row is empty
            if (empty(array_filter($data))) {
                break;
            }
            
            $check = array();
            
            // Get AFM from column 14 (index 13)
            $afm = $data[13] ?? null;

            if ($afm && str_contains($afm, "=")) {
                $check['afm'] = substr($afm, 2, -1);
            } else {
                $check['afm'] = $afm;
            }

            if (!Teacher::where('afm', $check['afm'])->count()) {
                unset($check);
            } else {
                $teacher = Teacher::where('afm', $check['afm'])->first();
                $check['teacher_id'] = $teacher->id;
                $check['director_surname'] = $teacher->surname;
                // Prepare directors array to pass it in session
                array_push($subdirectors_array, $check);
            }
            $row++;
        }
        fclose($handle);
    }
    
    session(['directors_array' => $directors_array]);
    session(['subdirectors_array' => $subdirectors_array]);
 
    if ($error) {
        return redirect(url('/insert_directors'))
            ->with('asks_to', 'save');
    } else {  
        return redirect(url('/insert_directors'))
            ->with('asks_to', 'save');
    }
}

public function insertDirectors(){
    $error = false;
    $done_at_least_once = false;
    $directors_array = session('directors_array');
    session()->forget('directors_array');
    $subdirectors_array = session('subdirectors_array');
    session()->forget('subdirectors_array');
    DB::table('teachers')->update(['is_director' => 0]);
    DB::table('teachers')->update(['is_subdirector' => 0]);
    
    // Update teachers's table with subdirectors if needed
    foreach ($subdirectors_array as $one_subdirector) {
        try {
            $subdirector = Teacher::find($one_subdirector['teacher_id']);
            $subdirector->is_subdirector = 1;
            if ($subdirector->isDirty()) {
                $subdirector->save();
                $done_at_least_once = true;
            }
            
        } catch (Throwable $e) {
            //Log::channel('throwable_db')->error(Auth::user()->username.' link director error '.$one_director['teacher_id'].' '.$e->getMessage());
            $error = true;
            continue;    
        }
    }
    
    // dd($directors_array);
    foreach ($directors_array as $one_director) {
        // Update schools records based on 'code' field
        try {
            $school = School::find($one_director['school_id']);
            $school_director = Teacher::find($one_director['teacher_id']);
            $school->director_id = $school_director->id;
            if ($school->isDirty()) {
                $school->save();
                $done_at_least_once = true;
            }
            $school_director->is_director = 1;
            if ($school_director->isDirty()) {
                $school_director->save();
                $done_at_least_once = true;
            }
        } catch (Throwable $e) {
            //dd($one_director);
            Log::channel('throwable_db')->error(Auth::user()->username.' link director error '.$one_director['school_name'].' '.$e->getMessage());
            $error = true;
            continue;    
        }
        // if($one_director['school_id']==222 and $one_director['teacher_id']==1570)dd($one_director);
    }
    
    if ($done_at_least_once) {
        DB::table('last_update_directors')->updateOrInsert(['id'=>1], ['date_updated'=>now()]);
        event(new SchoolsTeachersUpdated());
    }
    
    if (!$error) {
        Log::channel('user_memorable_actions')->info(Auth::user()->username.' insertDirectors');
        return redirect(url('/teachers'))
            ->with('success', 'Η εισαγωγή ολοκληρώθηκε');    
    } else {
        Log::channel('user_memorable_actions')->warning(Auth::user()->username.' insertDirectors with errors');
        return redirect(url('/teachers'))->with('warning', 'Η εισαγωγή ολοκληρώθηκε με σφάλματα που καταγράφηκαν στο log throwable_db'); 
    }
}

private function removeDuplicateDeputyDirectors($directors_array) {
    // Map to track which school codes have already seen an entry to keep
    $schoolsToKeep = [];
    $rowsToDelete = [];

    // First pass: Identify rows to keep or delete
    foreach ($directors_array as $key => $row) {
        $schoolCode = $row['code'];
        $isDeputy = $row['deputy_director'] === 'ΝΑΙ';
        $isPrivate = $row['public_school'] === 'Ιδιωτικά Σχολεία';
        
        // Keep rows of private schools to delete
        if ($isPrivate) {
            $rowsToDelete[] = $key; // Mark for deletion
        }
        // If this school code has already been marked for keeping, skip it
        if (isset($schoolsToKeep[$schoolCode])) {
            if ($isDeputy) {
                $rowsToDelete[] = $key; // Mark for deletion
            }
        } else {
            // Keep the first occurrence and ignore deputy entries
            if (!$isDeputy) {
                $schoolsToKeep[$schoolCode] = true;
            } else {
                $rowsToDelete[] = $key; // Mark initial deputy entry for deletion
            }
        }
    }

    // Second pass: Remove rows marked for deletion
    foreach ($rowsToDelete as $index) {
        unset($directors_array[$index]);
    }

    // Reindex the array after deletion
    $directors_array = array_values($directors_array);
    return $directors_array;
}
    // Helper function to get school's submission status
    public static function getSubmissionStatus($item, $submissionExists = false) {
            // Handle custom logic for daily absence reports with deadline everyday at 10:00 AM
            if($item->url == '/daily_absence_reports'){
                return ['status' => 'pending', 'color' => 'bg-blue-500', 'text' => 'text-white', 'badge' => 'Προς υποβολή'];
                $now = \Carbon\Carbon::now();
                $deadline = \Carbon\Carbon::today()->setHour(10)->setMinute(0)->setSecond(0);
                
                if ($submissionExists) {
                    return ['status' => 'completed', 'color' => 'bg-green-500', 'text' => 'text-white', 'badge' => 'Ολοκληρώθηκε'];
                }
                
                if ($now->greaterThan($deadline)) {
                    return ['status' => 'overdue', 'color' => 'bg-red-500', 'text' => 'text-white', 'badge' => 'Έληξε'];
                } else {
                    return ['status' => 'pending', 'color' => 'bg-blue-500', 'text' => 'text-white', 'badge' => 'Προς υποβολή'];
                }
            }
            if (!isset($item->closes_at) || empty($item->closes_at)) {
                return ['status' => 'no-deadline', 'color' => 'bg-gray-500', 'text' => 'text-white', 'badge' => 'Χωρίς προθεσμία'];
            }
            
            $deadline = \Carbon\Carbon::parse($item->closes_at);
            
            $now = \Carbon\Carbon::now();
            //$now = \Carbon\Carbon::create(2025, 9, 25, 15, 30, 0); // YYYY, MM, DD, HH, MM, SS

            $daysUntilDeadline = $now->diffInDays($deadline, false);
            
            if ($submissionExists) {
                return ['status' => 'completed', 'color' => 'bg-green-500', 'text' => 'text-white', 'badge' => 'Ολοκληρώθηκε'];
            }
            
            if ($now->toDateString() > $deadline->toDateString()) {
                return ['status' => 'overdue', 'color' => 'bg-red-500', 'text' => 'text-white', 'badge' => 'Εκπρόθεσμη'];
            }
            
            if ($daysUntilDeadline <= 3) {
                return ['status' => 'urgent', 'color' => 'bg-orange-500', 'text' => 'text-white', 'badge' => 'Λήγει σύντομα'];
            }
            
            return ['status' => 'pending', 'color' => 'bg-blue-500', 'text' => 'text-white', 'badge' => 'Προς υποβολή'];
        }

        public static function getSubmissionExists($microappOrFilecollect, School $school) {
            if($microappOrFilecollect instanceof \App\Models\Microapp) {// Handle Microapps
                $microapp = $microappOrFilecollect;
                if($microapp->url == '/all_day_school'){
                    $active_month = Month::getActiveMonth();
                    $vmonth = $school->vmonth;
                    $accepts = $microapp->accepts; 
                    $name = $microapp->name;
                    if(!$school->vmonth or $school->vmonth->vmonth == 0){
                        $month_to_store = $active_month->id;
                    }
                    else{
                        $month_to_store = $vmonth->vmonth;
                    }
                    $old_data = $school->all_day_schools->where('month_id', $month_to_store)->first();
                    if($old_data){
                        return true;
                    }
                    else{
                        return false;
                    }
                    
                } 

                if($microapp->url == '/enrollments'){
                    if($school->enrollments){
                        return true;
                    } else {
                        return false;
                    }
                }

                if($microapp->url == '/internal_rules'){
                    if($school->internal_rule){
                        return true;
                    } else {
                        return false;
                    }
                }

                if($microapp->url == '/immigrants'){
                    $active_month = Month::getActiveMonth();
                    $vmonth = $school->vmonth;
                    $accepts = $microapp->accepts; 
                    $name = $microapp->name;
                    if(!$school->vmonth or $school->vmonth->vmonth == 0){
                        $month_to_store = $active_month->id;
                    }
                    else{
                        $month_to_store = $vmonth->vmonth;
                    }
                    $old_data = $school->immigrants->where('month_id', $month_to_store)->first();
                    if($old_data){
                        if($old_data->no_refugees == 1 || $old_data->file){
                            return true;
                        }else{
                            return false;
                        }
                    }
                    else{
                        return false;
                    }
                }

                if($microapp->url == '/building_problems'){
                    if($school->buildingProblems){
                        return true;
                    } else {
                        return false;
                    }
                }

                if($microapp->url == '/daily_absence_reports'){
                    if($school->dailyAbsenceReport()->whereDate('report_date', Carbon::today())->exists()) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else { // Handle Filecollects
                $stakeHolder = $microappOrFilecollect;

                $old_data = $school->filecollects()->where('filecollect_id', $stakeHolder->filecollect_id)->exists() && $school->filecollects->where('filecollect_id', $stakeHolder->filecollect_id)->first()->file != null;
                
                if($old_data) {
                    return true; // Submission exists
                } else {
                    return false; // No submission found
                }
            }   
        }

        public static function getDaysRemaining($deadline) {
            if (!$deadline) return null;
            $date = \Carbon\Carbon::parse($deadline);
            $now = \Carbon\Carbon::now()->startOfDay(); // Ensure we compare only the date part
            $days = $now->diffInDays($date, false);
            //dd($deadline, $days);
            
            if ($days < 0) {
                if($days == -1) {
                    return 'Έληξε πριν 1 ημέρα';
                } else {
                return 'Έληξε πριν ' . abs($days) . ' ημέρες';
                }
            } else if ($days == 0) {
                return 'Λήγει σήμερα!';
            } else if ($days == 1) {
                return 'Λήγει αύριο';
            } else {
                return 'Απομένουν ' . $days . ' ημέρες';
            }
        }
}
