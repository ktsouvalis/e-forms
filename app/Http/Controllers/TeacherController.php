<?php

namespace App\Http\Controllers;

use DateTime;
use Throwable;
use App\Models\Form;
use App\Models\School;
use App\Models\Teacher;
use App\Models\NoSchool;
use App\Models\Directory;
use Illuminate\Http\Request;
use App\Models\SxesiErgasias;
use App\Models\WorkExperience;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Events\SchoolsTeachersUpdated;
use App\Models\microapps\TeacherLeaves;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TeacherController extends Controller
{
    public function login($md5){ 
        return redirect(url('/'))->with('warning', "Η σύνδεση γίνεται πλέον με τους κωδικούς στο ΠΣΔ. Πατήστε 'Σύνδεση Σχολείου / Εκπαιδευτικού' για να συνδεθείτε.");
        
        // $teacher = Teacher::where('md5', $md5)->firstOrFail();
        // //logs the teacher in using the 'teacher' guard
        // Auth::guard('teacher')->login($teacher);
        // session()->regenerate();
        // $teacher->logged_in_at = Carbon::now();   
        // $teacher->save();

        // return redirect(url('/index_teacher'))->with('success',"$teacher->name καλωσήρθατε!");
    }

    public function logout(){
        auth()->guard('teacher')->logout();
        return redirect(url('/'))->with('success', 'Αποσυνδεθήκατε');
    }

    /**
     * Import teacher assignment data (didaskalia or apousia) one at a time
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function import_didaskalia_apousia(Request $request)
    {
        $request->validate([
            'import_teachers' => 'required|mimetypes:text/csv,text/plain,application/csv',
            'template_file' => 'required|in:didaskalia,apousia'
        ]);
        // Determine configuration based on template type
        $config = $this->getImportConfig($request->input('template_file'));
        // Process the file and update teachers - returns true if errors occurred
        $error = $this->readFileAndUpdateTeachers($config, $request);

        $status = $error ? 'warning' : 'success';
        $message = $error 
            ? "Επιτυχής ενημέρωση {$config['description']} εκπαιδευτικών με σφάλματα που καταγράφηκαν στο log throwable_db"
            : "Επιτυχής ενημέρωση {$config['description']} εκπαιδευτικών";

        return redirect(url('/teachers'))->with($status, $message);
    }

    /**
     * Get import configuration based on template type
     *
     * @param string $templateType
     * @return array
     */
    private function getImportConfig(string $templateType): array
    {
        $configs = [
            'didaskalia' => [
                'type' => 'didaskalia',
                'description' => '1ου σχολείου υπηρέτησης',
                'column_index' => 7,
                'model' => School::class,
                'field' => 'code'
            ],
            'apousia' => [
                'type' => 'apousia',
                'description' => 'απουσίας',
                'column_index' => 45,
                'model' => NoSchool::class,
                'field' => 'name'
            ]
        ];

        return $configs[$templateType];
    }

    /**
     * Read CSV file and update teachers
     *
     * @param array $config Import configuration
     * @param Request $request The HTTP request object
     * @return bool Returns true if errors occurred, false otherwise
     */
    private function readFileAndUpdateTeachers(array $config, Request $request): bool
    {
        ini_set('max_execution_time', '300');
        
        $filename = "teachers_file_{$config['type']}_" . Auth::id() . ".csv";
        $path = $request->file('import_teachers')->storeAs('files', $filename);
        $filePath = "../storage/app/$path";

        $error = false;
        $wasChanged = false;

        $handle = fopen($filePath, "r");
        if ($handle === false) {
            Log::channel('throwable_db')->error("Failed to open file: $filePath");
            return true;
        }
        $absencesToIgnore = [
            'ΑΠΕΥΘΕΙΑΣ ΑΠΟΣΠΑΣΗ ΣΕ ΣΧΟΛΙΚΗ ΜΟΝΑΔΑ',
            'ΑΠΟΣΠΑΣΗ ΣΕ ΣΧΟΛΙΚΗ ΜΟΝΑΔΑ ΕΝΤΟΣ ΤΟΥ ΠΥΣΔΕ / ΠΥΣΠΕ',
            'ΕΠΙ ΘΗΤΕΙΑ ΣΕ ΣΧΟΛΙΚΗ ΜΟΝΑΔΑ',
            'ΟΛΙΚΗ ΔΙΑΘΕΣΗ ΣΕ ΑΛΛΗ ΣΧΟΛΙΚΗ ΜΟΝΑΔΑ'
        ];
        try {
            // Skip header row
            fgetcsv($handle, 0, ";");
            
            $rowNumber = 2;
            // Tell PHP this file is Windows-1253 encoded
            stream_filter_append($handle, 'convert.iconv.Windows-1253/UTF-8');
            
            while (($data = fgetcsv($handle, 0, ";")) !== false) {
                // Skip empty rows
                if (empty(array_filter($data))) {
                    break;
                }
                /** Ignore rows with ΑΠΕΥΘΕΙΑΣ ΑΠΟΣΠΑΣΗ ΣΕ ΣΧΟΛΙΚΗ ΜΟΝΑΔΑ, 
                 *                   ΑΠΟΣΠΑΣΗ ΣΕ ΣΧΟΛΙΚΗ ΜΟΝΑΔΑ ΕΝΤΟΣ ΠΥΣΠΕ/ΠΥΣΔΕ, 
                 *                   ΕΠΙ ΘΗΤΕΙΑ ΣΕ ΣΧΟΛΙΚΗ ΜΟΝΑΔΑ, 
                 *                   ΟΛΙΚΗ ΔΙΑΘΕΣΗ ΣΕ ΣΧ. ΜΟΝΑΔΑ.
                 *                   Πρέπει να σβηστούν τα ιδιωτικά σχολεία
                **/
                if ($config['type'] === 'apousia' && isset($data[45]) && in_array($data[45], $absencesToIgnore)) {
                    $rowNumber++;
                    continue;
                    
                }
                
                // Ignore rows with Private Schools
                if ($config['type'] === 'apousia' && isset($data[5]) && $data[5]=='Ιδιωτικά Σχολεία') {
                    $rowNumber++;   
                    continue;   
                }
                
                $afm = $this->sanitizeAFM($data[17] ?? null);
                if (!$afm) {
                    $rowNumber++;
                    continue;
                }
                
                $fieldValue = $this->sanitizeFieldValue($data[$config['column_index']] ?? null);
                if (!$fieldValue) {
                    $rowNumber++;
                    continue;
                }
                print_r("Processing row $rowNumber: AFM = $afm, Field Value = $fieldValue\n");

                    
                $updateResult = $this->updateTeacherAssignment(
                    $afm, 
                    $fieldValue, 
                    $config
                );
                
                if ($updateResult === false) {
                    $error = true;
                } elseif ($updateResult === true) {
                    $wasChanged = true;
                }
                
                $rowNumber++;
            }
        } finally {
            fclose($handle);
        }
        
        if ($wasChanged) {
            $this->updateLastModifiedTimestamp();
        }
        //dd('finished');
        return $error;
    }

    /**
     * Sanitize AFM value from CSV
     *
     * @param string|null $afm
     * @return string|null
     */
    private function sanitizeAFM(?string $afm): ?string
    {
        if (!$afm) {
            return null;
        }
        
        return str_contains($afm, '=') ? substr($afm, 2, -1) : $afm;
    }

    /**
     * Sanitize field value from CSV
     *
     * @param string|null $value
     * @return string|null
     */
    private function sanitizeFieldValue(?string $value): ?string
    {
        if (!$value) {
            return null;
        }
        
        return str_contains($value, '=') ? substr($value, 2, -1) : $value;
    }

    /**
     * Update teacher assignment
     *
     * @param string $afm Teacher's AFM
     * @param string $fieldValue The structure identifier
     * @param array $config Import configuration
     * @return bool|null Returns true if changed, false if error, null if no change
     */
    private function updateTeacherAssignment(string $afm, string $fieldValue, array $config): ?bool
    {
        $teacher = Teacher::where('afm', $afm)->first();
        
        if (!$teacher) {
            $this->logError(
                "update {$config['type']} afm error: $afm",
                "Δε βρέθηκε το ΑΦΜ $afm κατά την ενημέρωση: {$config['type']}",
                "Ενημέρωση {$config['type']}: Σφάλμα ΑΦΜ $afm"
            );
            return false;
        }
        
        $structure = $config['model']::where($config['field'], $fieldValue)->first();
        
        if (!$structure) {
            $modelName = class_basename($config['model']);
            $this->logError(
                "update {$config['type']} {$config['field']} error: $fieldValue",
                "Δε βρέθηκε φορέας ($fieldValue - $modelName) κατά την ενημέρωση: {$config['type']}",
                "Ενημέρωση {$config['type']}: Σφάλμα ΑΦΜ $afm"
            );
            return false;
        }
        
        $teacher->ypiretisi_id = $structure->id;
        $teacher->ypiretisi_type = $config['model'];
        
        if ($teacher->isDirty()) {
            $teacher->save();
            return true;
        }
        
        return null;
    }

    /**
     * Log error and notify user
     *
     * @param string $logMessage
     * @param string $notificationBody
     * @param string $notificationTitle
     * @return void
     */
    private function logError(string $logMessage, string $notificationBody, string $notificationTitle): void
    {
        Log::channel('throwable_db')->error($logMessage);
        Auth::user()->notify(new UserNotification($notificationBody, $notificationTitle));
    }

    /**
     * Update last modified timestamp for teachers
     *
     * @return void
     */
    private function updateLastModifiedTimestamp(): void
    {
        DB::table('last_update_teachers')->updateOrInsert(
            ['id' => 1],
            ['date_updated' => now()]
        );
        event(new SchoolsTeachersUpdated());
    }
    /**

    * Import and process teachers from CSV files simultaneously for organiki and apospasi
    *
    * @param Request $request The HTTP request object.
    * @return \Illuminate\Http\RedirectResponse The redirect response.
    */
    public function importTeachers(Request $request){

        $rule = [
            'organiki_file' => 'mimetypes:text/csv,text/plain,application/csv',
            'apospasi_file' => 'mimetypes:text/csv,text/plain,application/csv'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου (Επιτρεπτός τύπος: csv)');
        }

        //store the files
        $filename = "teachers_file_organiki".Auth::id().".csv";
        $path = $request->file('organiki_file')->storeAs('files', $filename);
        $filename2 = "teachers_file_apospasi".Auth::id().".csv";
        $path2 = $request->file('apospasi_file')->storeAs('files', $filename2);

        //load the organiki file
        $teachers_array = array();
        $error = false;
        $wasChanged = false;
        
        if (($handle = fopen("../storage/app/$path", "r")) !== FALSE) {
            // Skip header row
            fgetcsv($handle, 0, ";");
            $rowCounter = 1;
            // Tell PHP this file is Windows-1253 encoded
            stream_filter_append($handle, 'convert.iconv.Windows-1253/UTF-8');
            while (($row = fgetcsv($handle, 0, ";")) !== FALSE) {
                $rowCounter++;
                $check = array();
                
                $check['name'] = $row[4] ?? null; // Column 5 (index 4)
                $check['surname'] = $row[3] ?? null; // Column 4
                $check['fname'] = $row[5] ?? null; // Column 6
                $check['mname'] = $row[6] ?? null; // Column 7
                
                // Handle AFM - myschool stores the afm like eg "=999999999"
                $afm = $row[1] ?? null; // Column 2
                $check['afm'] = str_contains($afm, '=') ? substr($afm, 2, -1) : $afm;

                // Check obvious fields
                $check['gender'] = $row[2] ?? null; // Column 3
                $check['telephone'] = $row[10] ?? null; // Column 11
                $check['mail'] = $row[12] ?? null; // Column 13
                $check['sch_mail'] = $row[13] ?? null; // Column 14
                $check['klados'] = $row[14] ?? null; // Column 15
                $check['am'] = $row[0] ?? null; // Column 1
                
                // Handle appointment date
                $dateString = $row[21] ?? null; // Column 22
                if ($dateString) {
                    // Try to parse the date
                    $parsedDate = $this->parseDateString($dateString);
                    
                    if ($parsedDate) {
                        $check['appointment_date'] = $parsedDate->format('Y-m-d');
                        Log::channel('login_as')->info('Επώνυμο: ', [
                            'surname' => $check['surname'],
                            'date' => $parsedDate->format('Y-m-d')
                        ]);
                    } else {
                        $check['appointment_date'] = null;
                        Log::channel('login_as')->info('Επώνυμο: ', [
                            'surname' => $check['surname'],
                            'date_not_detected' => $dateString
                        ]);
                    }
                } else {
                    $check['appointment_date'] = null;
                }
                
                $check['appointment_fek'] = $row[20] ?? null; // Column 21
                
                // Cross check sxesi_ergasias with database
                $sxesi = $row[48] ?? null; // Column 49
            
                if ($sxesi && SxesiErgasias::where('name', $sxesi)->count()) {
                    try{
                        $check['sxesi_ergasias'] = SxesiErgasias::where('name', $sxesi)->first()->id;
                        $check['sxesi_ergasias_name'] = SxesiErgasias::where('name', $sxesi)->first()->name;
                    } catch (Throwable $e) {
                        Log::channel('throwable_db')->error($check['afm'].' '.$e->getMessage());
                        $error = true;
                        continue;
                    }
                    
                } else {
                    $error = true;
                    $check['sxesi_ergasias'] = "Error: Άγνωστη Σχέση Εργασίας";
                    Auth::user()->notify(new UserNotification("Error: Άγνωστη Σχέση Εργασίας κατά την ενημέρωση Οργανικής για το ΑΦΜ: ".$check['afm'], "Ενημέρωση οργανικής: Σφάλμα ΑΦΜ ".$check['afm']));
                    continue;
                }
                
                // Handle organiki - myschool stores the organiki like eg "=999999999"
                $organiki = $row[35] ?? null; // Column 36
                $sanitized_organiki = $organiki;
                if (str_contains($organiki, '=')) {
                    $sanitized_organiki = substr($organiki, 2, -1);
                }

                $check['org_eae'] = 1;
                if (isset($row[54]) && $row[54] == "ΟΧΙ") { // Column 55
                    $check['org_eae'] = 0;
                }

                // Check if organiki is in a school or Directory
                if (School::where('code', $sanitized_organiki)->count()) {
                    $check['organiki'] = School::where('code', $sanitized_organiki)->first()->id;
                    $check['organiki_name'] = School::where('code', $sanitized_organiki)->first()->name;
                    $check['organiki_type'] = "App\Models\School";
                } else if (Directory::where('code', $sanitized_organiki)->count()) {
                    $check['organiki'] = Directory::where('code', $sanitized_organiki)->first()->id;
                    $check['organiki_name'] = Directory::where('code', $sanitized_organiki)->first()->name;
                    $check['organiki_type'] = "App\Models\Directory";    
                } else {
                    // If no school and no directory found, save the code from the directorate_info table 
                    $dir_code = DB::table('directorate_info')->find(1)->code;
                    $check['organiki'] = Directory::where('code', $dir_code)->first()->id;
                    $check['organiki_name'] = Directory::where('code', $dir_code)->first()->name;
                    $check['organiki_type'] = "App\Models\Directory"; 
                }
                
                array_push($teachers_array, $check);
            }
            fclose($handle);
        }

        // Load the apospasi file
        if (($handle = fopen("../storage/app/$path2", "r")) !== FALSE) {
            // Skip header row
            fgetcsv($handle, 0, ";");
            
            $rowNum = 2;
            // Tell PHP this file is Windows-1253 encoded
            stream_filter_append($handle, 'convert.iconv.Windows-1253/UTF-8');
            while (($row = fgetcsv($handle, 0, ";")) !== FALSE && $rowNum < 10000) {
                // Check if row is empty
                if (empty(array_filter($row))) {
                    break;
                }
                
                $check = array();
                
                $check['name'] = $row[4] ?? null;
                $check['surname'] = $row[3] ?? null;
                $check['fname'] = $row[5] ?? null;
                $check['mname'] = $row[6] ?? null;
                
                // Handle AFM
                $afm = $row[1] ?? null;
                $check['afm'] = str_contains($afm, '=') ? substr($afm, 2, -1) : $afm;
                    
                // Check obvious fields
                $check['gender'] = $row[2] ?? null;
                $check['telephone'] = $row[10] ?? null;
                $check['mail'] = $row[12] ?? null;
                $check['sch_mail'] = $row[13] ?? null;
                $check['klados'] = $row[14] ?? null;
                $check['am'] = $row[0] ?? null;
                
                // Handle appointment date
                $dateString = $row[21] ?? null;
                if ($dateString) {
                    $parsedDate = $this->parseDateString($dateString);
                    if ($parsedDate) {
                        $check['appointment_date'] = $parsedDate->format('Y-m-d');
                    } else {
                        $check['appointment_date'] = null;
                    }
                } else {
                    $check['appointment_date'] = null;
                }
                
                $check['appointment_fek'] = $row[20] ?? null;
                
                // Cross check sxesi_ergasias with database
                $sxesi = $row[48] ?? null;
                if ($sxesi && SxesiErgasias::where('name', $sxesi)->count()) {
                    $check['sxesi_ergasias'] = SxesiErgasias::where('name', $sxesi)->first()->id;
                    $check['sxesi_ergasias_name'] = SxesiErgasias::where('name', $sxesi)->first()->name;
                } else {
                    $error = true;
                    $check['sxesi_ergasias'] = "Error: Άγνωστη Σχέση Εργασίας";
                    Auth::user()->notify(new UserNotification("Error: Άγνωστη Σχέση Εργασίας στη γραμμή $rowNum κατά την ενημέρωση Απόσπασης για το ΑΦΜ: ".$check['afm'], "Ενημέρωση απόσπασης: Σφάλμα ΑΦΜ ".$check['afm']));
                    $rowNum++;
                    continue;
                }

                $ignore_record = 0;
                
                $check['org_eae'] = 1;
                if (isset($row[50]) && $row[50] == "ΟΧΙ") {
                    $check['org_eae'] = 0;
                }

                // Fix directories to match database and then cross check
                $organiki = $row[23] ?? null; // Column 24
                $newString = $organiki;
                
                if ($organiki != DB::table('directorate_info')->find(1)->name) {
                    if (Directory::where('name', $newString)->count()) {
                        $check['organiki'] = Directory::where('name', $newString)->first()->id;
                        $check['organiki_name'] = Directory::where('name', $newString)->first()->name;
                        $check['organiki_type'] = "App\Models\Directory";
                    } else {
                        $check['organiki'] = Directory::where('name', 'ΠΕΡΙΦΕΡΕΙΑΚΗ Δ/ΝΣΗ Π/ΘΜΙΑΣ ΚΑΙ Δ/ΘΜΙΑΣ ΕΚΠ/ΣΗΣ ΔΥΤΙΚΗΣ ΕΛΛΑΔΑΣ')->first()->id;
                        $check['organiki_name'] = Directory::where('name', 'ΠΕΡΙΦΕΡΕΙΑΚΗ Δ/ΝΣΗ Π/ΘΜΙΑΣ ΚΑΙ Δ/ΘΜΙΑΣ ΕΚΠ/ΣΗΣ ΔΥΤΙΚΗΣ ΕΛΛΑΔΑΣ')->first()->name;
                        $check['organiki_type'] = "App\Models\Directory";
                        Auth::user()->notify(new UserNotification("Error: Άγνωστος κωδικός οργανικής στη γραμμή $rowNum κατά την ενημέρωση Απόσπασης για το ΑΦΜ ".$check['afm'], "Ενημέρωση απόσπασης: Σφάλμα ΑΦΜ ".$check['afm']));
                    }
                } else {
                    $ignore_record = 1;  // Ignore those that belong to ΑΧΑΪΑ because they are in the database through the 4.1 report (organiki) 
                }
                
                // Add to array if not ignored
                if (!$ignore_record) {
                    array_push($teachers_array, $check);
                }

                $rowNum++;
            }
            fclose($handle);
        }

        // Now process the teachers array and insert/update to database
        foreach($teachers_array as $teacher){
            try{
                $teacherModel = Teacher::updateOrCreate(
                    [
                        'afm'=> $teacher['afm'] 
                    ],
                    [
                        'md5' => md5($teacher['afm']),
                        'name'=> $teacher['name'],
                        'surname'=> $teacher['surname'],
                        'fname' => $teacher['fname'],
                        'mname' => $teacher['mname'],
                        'afm' => $teacher['afm'],
                        'gender' => $teacher['gender'],
                        'telephone' => $teacher['telephone'],
                        'mail' => $teacher['mail'],
                        'sch_mail' => $teacher['sch_mail'],
                        'klados' => $teacher['klados'],
                        'am' => $teacher['am'],
                        'sxesi_ergasias_id' => $teacher['sxesi_ergasias'],
                        'org_eae' => $teacher['org_eae'],
                        'organiki_id' => $teacher['organiki'],
                        'organiki_type' => $teacher['organiki_type'],
                        'appointment_date' => $teacher['appointment_date'],
                        'appointment_fek' => $teacher['appointment_fek'],
                        'active'=>1
                    ]
                );
                
                if($teacherModel->wasRecentlyCreated || $teacherModel->wasChanged()){
                    $wasChanged = true;
                }
            }
            catch(Throwable $e){
                Log::channel('throwable_db')->error($teacher['afm'].' '.$e->getMessage());
                $error = true;
                continue; 
            }
        }
        
        // Make not active the teachers that exist in database but not in 4.1 and 4.2
        Teacher::whereNotIn('afm', collect($teachers_array)->pluck('afm'))->update(['active' => 0]);
        
        if($wasChanged){
            DB::table('last_update_teachers')->updateOrInsert(['id' => 1],['date_updated' => now()]);
            event(new SchoolsTeachersUpdated());
        }
        
        if(!$error){
            Log::channel('user_memorable_actions')->info(Auth::user()->username.' importTeachers');
            return redirect(url('/teachers'))
                ->with('success', 'Η εισαγωγή ολοκληρώθηκε');
        }
        else{
            Log::channel('user_memorable_actions')->warning(Auth::user()->username.' importTeachers with errors');
            return redirect(url('/teachers'))
                ->with('warning', 'Η εισαγωγή ολοκληρώθηκε με σφάλματα που καταγράφηκαν στο log throwable_db');
        }
    }
    public function import_work_experience(Request $request) {
        
        //validate the input file type
        $rule = [
            'work_experience_file' => 'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου (Επιτρεπτός τύπος: xlsx)');
        }
        //store the files
        $filename = "teachers_files_work_experience".Auth::id().".xlsx";
        $path = $request->file('work_experience_file')->storeAs('files', $filename);
        //load the file
        $spreadsheet = IOFactory::load("../storage/app/$path");
        $teachers_array=array();
        $row=2;
        $error=0;
        $rowSumValue="1";
        $validationDate = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(38, 1)->getValue();
        if($validationDate !== "Εκπαιδευτική Υπηρεσία μέχρι και 31/8/2026 Έτη"){
            $messg = 'Η ημερομηνία υπολογισμού της προϋπηρεσίας είναι "'.$validationDate.'" αντί για 31/8/2026';
            return back()->with('failure', $messg);
        }
       
        while ($rowSumValue != "" && $row<10000){
            $check=array();
            $check['years'] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(38, $row)->getValue();
            $check['months']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(39, $row)->getValue();
            $check['days']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(40, $row)->getValue();
            //myschool stores the afm like eg "=999999999"
            $afm = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2, $row)->getValue();
            $check['afm']= substr($afm, 2, -1); // remove from start =" and remove from end "
            array_push($teachers_array, $check);
            $row++;
            $rowSumValue="";
            for($col=1;$col<=54;$col++){
                $rowSumValue .= $spreadsheet->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();   
            }
        }
        
        //write data to db
        foreach($teachers_array as $teacher){
            $teacherModel = Teacher::where('afm', $teacher['afm'])->first();
            if($teacherModel){
               
                try{
                    $workExperienceModel = WorkExperience::updateOrcreate(
                        [
                            'teacher_id'=> $teacherModel->id 
                        ],
                        [
                            'years' => $teacher['years'],
                            'months'=> $teacher['months'],
                            'days'=> $teacher['days'],
                        ]
                    );
                }
                catch(Throwable $e){
                    //dd($e->getMessage());
                    // Log::channel('throwable_db')->error(Auth::user()->username.' create teacher error '.$teacher['afm']);
                    Log::channel('throwable_db')->error($teacher['afm'].' '.$e->getMessage());
                    // Auth::user()->notify(new UserNotification("Κατά την εισαγωγή του εκπαιδευτικού με ΑΦΜ ".$teacher['afm']." προέκυψε το σφάλμα ".$e->getMessage(), "Εισαγωγή εκπαιδευτικών: Σφάλμα ΑΦΜ ".$teacher['afm']));
                    $error=true;
                    continue; 
                }
            }
        }
        if(!$error)
            return redirect(url('/teachers'))->with('success', "Επιτυχής ενημέρωση εκπαιδευτικών");
        else
            return redirect(url('/teachers'))->with('warning', "Επιτυχής ενημέρωση εκπαιδευτικών με σφάλματα που καταγράφηκαν στο log throwable_db");
        
    }

    public function import_leaves(Request $request){
        
        $file = $request->file('leaves_file');
        //validate the input file type
        $rule = [
            'leaves_file' => 'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]; 
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){
           return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου (Επιτρεπτός τύπος: xlsx)');
        }
        // truncate table
        // DB::statement('TRUNCATE TABLE teacher_leaves');
        // store the files
        $filename = "teachers_file_leaves".Auth::id().".xlsx";
        $path = $request->file('leaves_file')->storeAs('files', $filename);
        $error = 0;
        //load the file
        $spreadsheet = IOFactory::load("../storage/app/$path");
        $teachers_array=array();
        $row=2;
        $error=0;
        $rowSumValue="1";
        //dd('reached');
        while ($rowSumValue != "" && $row<500){
            $teacherAfm = substr($spreadsheet->getActiveSheet()->getCellByColumnAndRow(2, $row)->getValue(), 2, -1);
            if(!Teacher::where('afm', $teacherAfm)->count()){
                $row++;
                $rowSumValue="";
                for($col=1;$col<=35;$col++){
                    $rowSumValue .= $spreadsheet->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();   
                }
                Log::channel('throwable_db')->error("update leaves afm error: ".$teacherAfm);
                //Auth::user()->notify(new UserNotification("Δε βρέθηκε το ΑΦΜ $teacherAfm κατά την ενημέρωση αδειών", "Ενημέρωση αδειών: Σφάλμα ΑΦΜ $teacherAfm"));
                //$error=true;
                continue;
            }
            try{
                $teacherLeaveTuple = TeacherLeaves::updateOrcreate(
                    [
                        'afm'=> $teacherAfm,
                        'leave_type' => $spreadsheet->getActiveSheet()->getCellByColumnAndRow(16, $row)->getValue(),
                        'leave_start_date' => TeacherController::convertExcelDate($spreadsheet->getActiveSheet()->getCellByColumnAndRow(17, $row)),
                        'leave_days' => $spreadsheet->getActiveSheet()->getCellByColumnAndRow(18, $row)->getValue(),
                    ],
                    [
                        'am' => $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue(),
                        'sex'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(3, $row)->getValue(),
                        'surname'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(4, $row)->getValue(),
                        'name'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(5, $row)->getValue(),
                        'fathers_name'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(6, $row)->getValue(),
                        'specialty_code'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(7, $row)->getValue(),
                        'specialty'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(8, $row)->getValue(),
                        'directorate'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(12, $row)->getValue(),
                        'employment_relation'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(14, $row)->getValue(),
                        'leave_state'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(15, $row)->getValue(),
                        'leave_protocol_number'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(19, $row)->getValue(),
                        'leave_protocol_date'=> TeacherController::convertExcelDate($spreadsheet->getActiveSheet()->getCellByColumnAndRow(20, $row)),
                        'leave_description'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(21, $row)->getValue(),
                        'creator_entity_code'=> substr($spreadsheet->getActiveSheet()->getCellByColumnAndRow(22, $row)->getValue(), 2, -1),
                        'creator_entity_name'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(23, $row)->getValue(),
                        'creation_date'=> TeacherController::convertExcelDate($spreadsheet->getActiveSheet()->getCellByColumnAndRow(24, $row)),
                        'submission_date'=> TeacherController::convertExcelDate($spreadsheet->getActiveSheet()->getCellByColumnAndRow(25, $row)),
                        'approved_days'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(26, $row)->getValue(),
                        'approved_months'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(27, $row)->getValue(),
                        'approved_years'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(28, $row)->getValue(),
                        'approved_protocol_number'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(29, $row)->getValue(),
                        'approved_protocol_date'=> TeacherController::convertExcelDate($spreadsheet->getActiveSheet()->getCellByColumnAndRow(30, $row)),
                        'approved_description'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(31, $row)->getValue(),
                        'revoke_description'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(32, $row)->getValue(),
                        'approving_authority_code'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(33, $row)->getValue(),
                        'approving_authority_name'=> $spreadsheet->getActiveSheet()->getCellByColumnAndRow(34, $row)->getValue(),
                        'last_change_date'=> TeacherController::convertExcelDate($spreadsheet->getActiveSheet()->getCellByColumnAndRow(35, $row)),
                    ]
                );
            } catch(Throwable $e){
                Log::channel('throwable_db')->error($teacherAfm.' '.$e->getMessage());
                $error=true;
                continue; 
            }
            
            $row++;
            $rowSumValue="";
            for($col=1;$col<=35;$col++){
                $rowSumValue .= $spreadsheet->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();   
            }
        }
        if(!$error)
            return redirect(url('/teachers'))->with('success', "Επιτυχής ενημέρωση αδειών εκπαιδευτικών");
        else
            return redirect(url('/teachers'))->with('warning', "Ενημέρωση αδειών εκπαιδευτικών με σφάλματα που καταγράφηκαν στο log throwable_db");
        
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

    private function parseDateString($dateString)
    {
        // Remove any whitespace
        $dateString = trim($dateString);
        
        // Try different date formats
        $formats = [
            'd/m/Y',    // 16/08/2007
            'd/m/y',    // 16/08/07
            'd-m-Y',    // 16-08-2007
            'd-m-y',    // 16-08-07
            // 2007-08-16
            // 08/16/2007
            // 08/16/07
        ];
        
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $dateString);
            if ($date && $date->format($format) === $dateString) {
                return $date;
            }
        }
        
        // Try strtotime as a fallback
        $timestamp = strtotime($dateString);
        if ($timestamp !== false) {
            $date = new DateTime();
            $date->setTimestamp($timestamp);
            return $date;
        }
        
        return null;
    }

    public function sendTeachersData(Request $request, $teacherId) {
        
        $token = $request->header('X-CSRF-TOKEN');
        
        if (!$token || $token !== env('HARDCODED_TOKEN')) {
             return response()->json(['error' => 'Invalid token'], 401);
        }

        // if(!Auth::check('user')) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }
        $teacher = Teacher::where('id', $teacherId)->first();
        if(!$teacher){
            return response()->json(['error' => 'Teacher not found'], 404);
        }
        return response()->json($teacher);
    }

}