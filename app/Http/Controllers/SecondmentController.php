<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\School;
use GuzzleHttp\Client;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\microapps\Secondment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\FilesController;

class SecondmentController extends Controller
{
    //
    public function index()
    {
        if(Auth::guard()->check()){
            return view('microapps.secondments.index');
        }
        
    }
    //Επεξεργασία, προσωρινή αποθήκευση, προεπισκόπηση και οριστική υποβολή αίτησης
    public function update(Secondment $secondment, Request $request){
        // dd($request->input('action'));
        if($request->input()['criteriaOrPreferences'] == 1){        //Αποθήκευση μοριοδοτούμενων κριτηρίων
            if($request->input('action') == "submit"){ //Ζητάει οριστική υποβολή κριτηρίων
                // Αποθήκευσε την αίτηση
                try{
                    $secondment->update($request->input());
                } catch(\Exception $e) {
                    //dd($e->getMessage());
                    return back()->with('failure', 'Αποτυχία αποθήκευσης αίτησης.');
                }
                //Δημιούργησε το pdf
                try{
                    $this->createPDF($secondment, $selectionOrder=null);
                } catch(\Exception $e) {
                    //dd($e->getMessage());
                    return back()->with('failure', 'Απέτυχε η δημιουργία του pdf αρχείου. Προσπαθήστε ξανά. Σε περίπτωση προβλήματος παρακαλούμε για την αποστολή mail στο it@dipe.ach.sch.gr.');
                }
                //Στείλε την αίτηση στο πρωτόκολλο
                try{
                    $protocol_message = $this->sendSecondmentToProtocol($secondment , $crOrPreferences = 1);
                    if($protocol_message == false){
                        return back()->with('failure', 'Η αίτηση αποθηκεύτηκε αλλά απέτυχε η αποστολή στο πρωτόκολλο. Παρακαλούμε για την αποστολή mail στο it@dipe.ach.sch.gr.');
                    }
                    $protocol_message = explode(" - ", $protocol_message);
                    $secondment->protocol_nr = $protocol_message[0];
                    $secondment->protocol_date = $protocol_message[1];
                    $secondment->save();
                } catch(\Exception $e) {
                    //dd($e->getMessage());
                    return back()->with('failure', 'Αποτυχία αποστολής αίτησης στο Πρωτόκολλο του ΠΥΣΠΕ. Παρακαλούμε επικοινωνήστε με το Τμήμα Πληροφορικής στο it@dipe.ach.sch.gr.');
                }
                //Οριστικοποίησε την αίτηση - criteria_submitted = 1
                try{
                    $secondment->criteria_submitted = 1;
                    $secondment->save();
                } catch(\Exception $e) {
                    //dd($e->getMessage());
                    return back()->with('failure', 'Η αίτηση αποθηκεύτηκε, πρωτοκολλήθηκε με επιτυχία στο Πρωτόκολλο του ΠΥΣΠΕ αλλά απέτυχε η οριστικοποίησή της. Παρακαλούμε επικοινωνήστε άμεσα με το Τμήμα Πληροφορικής στο  it@dipe.ach.sch.gr.');
                }
                return back()->with('success', 'Επιτυχής αποθήκευση αίτησης. Η αίτησή σας πρωτοκολλήθηκε αυτόματα στο Ηλεκτρονικό Πρωτόκολλο του ΠΥΣΠΕ Αχαΐας με αρ. πρωτ.: '. $secondment->protocol_nr . '-' . $secondment->protocol_date. '.');
            } else {    //Τέλος οριστικής υποβολής κριτηρίων
                //Zητάει μόνο αποθήκευση
                try{
                    $secondment->update($request->input());
                } catch(\Exception $e) {
                    //dd($e->getMessage());
                    return back()->with('failure', 'Αποτυχία αποθήκευσης αίτησης.');
                }
                return back()->with('success', 'Επιτυχής αποθήκευση αίτησης.');
            } //ΤΕΛΟΣ ΑΠΟΘΗΚΕΥΣΗΣ ΚΡΙΤΗΡΙΩΝ 
        //ΤΕΛΟΣ ΚΡΙΤΗΡΙΩΝ
        } else { //ΑΡΧΗ ΑΠΟΘΗΚΕΥΣΗ ΠΡΟΤΙΜΗΣΕΩΝ, οριστικοποίηση και αποστολή
            //Αν έχει συμπληρώσει επιλογές στον πίνακα προτιμήσεων πάρε τις προτιμήσεις με τη σειρά που έχουν συμπληρωθεί
            if(isset($request->input()['schools-select'])){
                $secondment->preferences_json = $request->input()['selectionOrder'];//φέρε την πραγματική σειρά που έχει συμπληρωθεί με javascript κάθε φορά που αλλάζει σχολεία
            } else {
                $secondment->preferences_json = null;
            }
            if($request->action == 'preview'){//Αν ζητάει προεπισκόπηση φύγε δείχνοντας την προεπισκόπηση
                return view('microapps.secondments.toPDF', ['secondment' => $secondment, 'selectionOrder' => $request->input()['selectionOrder']]);
            }
            //Δε ζητάει προεπισκόπηση - ζητάει αποθήκευση ή οριστική υποβολή - Αποθήκευσε τα στοιχεία
            try{
                //Αποθήκευσε τις προτιμήσεις
                $secondment->update($request->input());
                if($request->action == 'update'){ //Αν ζητάει μόνο αποθήκευση επέστρεψε με μήνυμα επιτυχίας
                    return back()->with('success', 'Επιτυχής αποθήκευση αίτησης.');
                }
                //Αν ζητάει οριστική υποβολή αλλά έχει κενές τις προτιμήσεις, έχοντας κρατήσει τις τιμές, επέστρεψε με μήνυμα αποτυχίας
                if($request->action == 'submit' && $request->input()['selectionOrder']=="[]"){
                    return back()->with('failure', 'Δε μπορεί να πραγματοποιηθεί οριστική υποβολή χωρίς προτιμήσεις. Αν επιθυμείτε να ακυρώσετε την αίτησή σας παρακαλούμε αφήστε τη σε κατάσταση προσωρινής αποθήκευσης και δε θα ληφθεί υπόψη.');
                }
                
            } catch(\Exception $e) {
                //dd($e->getMessage());
                return back()->with('failure', 'Αποτυχία αποθήκευσης αίτησης.');
            }
            //Αφού δεν έχει γυρίσει ούτε από preview ούτε από update, ζητάει οριστική υποβολή και έχει συμπληρώσει τις προτιμήσεις
            //Δημιούργησε το pdf
            try{
                $this->createPDF($secondment, $request->input()['selectionOrder']);
            } catch(\Exception $e) {
                //dd($e->getMessage());
                return back()->with('failure', 'Η αίτηση αποθηκεύτηκε αλλά απέτυχε η δημιουργία του pdf αρχείου. Προσπαθήστε ξανά. Σε περίπτωση προβλήματος παρακαλούμε για την αποστολή mail στο it@dipe.ach.sch.gr.');
            }
            //Στείλε τις προτιμήσεις στο πρωτόκολλο
            $protocol_message = $this->sendSchoolsToProtocol($secondment, $crOrPreferences = 2);
            if($protocol_message == false){
                return back()->with('failure', 'Η αίτηση αποθηκεύτηκε αλλά απέτυχε η αποστολή στο πρωτόκολλο. Προσπαθήστε ξανά. Σε περίπτωση προβλήματος παρακαλούμε για την αποστολή mail στο it@dipe.ach.sch.gr.');
            }
            //Ανανέωσε το submitted
            try{
                $secondment->submitted = 1;
                $secondment->save();
            } catch(\Exception $e) {
                //dd($e->getMessage());
                return back()->with('failure', 'Η δήλωση προτιμήσεων αποθηκεύτηκε, στάλθηκε με επιτυχία στο Πρωτόκολλο του ΠΥΣΠΕ αλλά απέτυχε η οριστικοποίησή της. Επικοινωνήστε άμεσα με το Τμήμα Πληροφορικής στο it@dipe.ach.sch.gr.');
            }
            return back()->with('success', 'Επιτυχής Οριστική Υποβολή αίτησης δήλωσης Σχολείων. Η αίτησή σας μαζί με τη δήλωση Σχολείων έχει πρωτοκολληθεί αυτόματα στο Ηλεκτρονικό Πρωτόκολλο του ΠΥΣΠΕ Αχαΐας με αρ. πρωτ.: '. $secondment->protocol_nr . '-' . $secondment->protocol_date. '.');
        }  //ΤΕΛΟΣ ΑΠΟΘΗΚΕΥΣΗΣ ΠΡΟΤΙΜΗΣΕΩΝ, οριστικοποίηση και αποστολή στο Πρωτόκολλο
    }

    public function create() 
    {
        if(Auth::guard('teacher')->user()->secondment()){
            $secondment = Auth::guard('teacher')->user()->secondment();
            return redirect(route('secondments.edit', ['secondment' => $secondment->id, 'criteriaOrPreferences' => 1]));
        }
        
        return view('microapps.secondments.create');
    }
    //Επεξεργασία αίτησης
    public function edit(Secondment $secondment, Request $request)
    {   if($request->input()){
            $criteriaOrPreferences = $request->input()['criteriaOrPreferences'];
        } else {
            $criteriaOrPreferences = 1;
        }
        if($criteriaOrPreferences == 1){
            return view('microapps.secondments.edit_criteria', ['secondment' => $secondment]);
        } else if ($criteriaOrPreferences == 2){
            if($secondment->criteria_submitted == 0){
                return back()->with('failure', 'Πρέπει πρώτα να οριστικοποιήσετε τα μοριοδοτούμενα κριτήρια πριν προχωρήσετε στις προτιμήσεις.');
            }
            if(!in_array($secondment->teacher->klados, ["ΠΕ70", "ΠΕ60", "ΠΕ71", "ΠΕ70.50", "ΠΕ60.50"])){
                return back()->with('failure', 'Η δήλωση Σχολείων για Εκπαιδευτικούς ειδικοτήτων θα πραγματοποιηθεί μετά την ανακοίνωση των Σχολείων.');
            }
            return view('microapps.secondments.edit_preferences', ['secondment' => $secondment]);
        }
    }
    //Δημιουργία πρώτης αίτησης
    public function store(Request $request)
    {
        //Αν έχει επιλέξει την υπεύθυνη δήλωση Δημιούργησε την αίτηση
        if(!isset($request->statement_of_declaration))
        {
            return back()->with('failure', 'Πρέπει να συμπληρώσετε την υπεύθυνη δήλωση. Σε περίπτωση που είστε σε θέση ευθύνης, 
            δε μπορείτε να υποβάλλετε αίτηση απόσπασης.');
        }
        else
        {
            try{
                $teacher = Auth::guard('teacher')->user();
                $appForRep = isset($request->application_for_reposition)? true : false;
                $secondment = Secondment::updateOrCreate(
                    ['teacher_id' => $teacher->id,
                    'revoked' => 0],
                    ['statement_of_declaration' => true,
                     'application_for_reposition' => $appForRep,
                    ]
                );
                return redirect(route('secondments.edit', ['secondment' => $secondment->id]))->with('success',"Επιτυχής αποθήκευση αίτησης. Μπορείτε να προχωρήσετε σε δήλωση μοριοδοτούμενων κριτηρίων.");
            } catch(\Exception $e) {
                //dd($e);
                return back()->with('failure', 'Αποτυχία αποθήκευσης αίτησης.');
            }
        }
        
    }

    public function modify(Secondment $secondment){
        $secondment->submitted = 0;
        $secondment->save();
        return back()->with('success', 'Η δήλωση σχολείων ενεργοποιήθηκε για τροποποίηση. Μπορείτε να την επεξεργαστείτε και να την υποβάλλετε ξανά.');
    }
    //Διαγραφή αρχείου
    public function delete_file(Secondment $secondment, $serverFileName){
        $fileHandler = new FilesController();
        $files = json_decode($secondment->files_json, true);
        try{
            $fileHandler->delete_file('secondments', $serverFileName, 'local');
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
    //Ανέβασμα αρχείων
    public function upload_files(Request $request, Secondment $secondment){
        $request->validate([ //Έλεγξε τον τύπο των αρχείων και το μέγεθός τους
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        $files = $request->file('files');
        $fileNames = [];
        //Βρες πόσα αρχεία έχει ήδη ανεβάσει
        if($secondment->files_json){ // Αν έχει ανεβάσει ήδη, βρες τον αριθμό του τελευταίου αρχείου από το όνομά του
            $fileNames = json_decode($secondment->files_json, true);
            end($fileNames);
            $lastServerFileName = key($fileNames);
            $underScorePosition = strpos($lastServerFileName, '_');// βρες τον αριθμό που περιλαμβάνεται στο όνομα του τελευταίου αρχείου μετά το _
            $filesCount = substr($lastServerFileName, $underScorePosition + 1, strpos($lastServerFileName, '.') - $underScorePosition -1);
        } else { //Αν δεν έχει ανεβάσει ακόμη αρχεία, βάλε τον αριθμό 0
            $filesCount = 0;
        }
        $lastFileNumber = $filesCount; // κράτα τον αριθμό του τελευταίου αρχείου για την περίπτωση που θα ανεβάσει επιπλέον αρχεία
        $directory = "secondments";
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
        $secondment->files_json = json_encode($fileNames);
        try{
            $secondment->save();
        } catch(\Exception $e) {
            //dd($e->getMessage());
            Log::channel('files')->error($teacherAfm." Secondment Files failed to update database field files_json");
            return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων με τα ονόματα των αρχείων. Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($teacherAfm." Secondment Files successfully uploaded");
        //Αν είμαστε σε κατάσταση που επιτρέπεται η υποβολή επιπλέον αρχείων
        $extraFilesToProtocol = false;
        if($secondment->criteria_submitted == 1 && $secondment->extra_files_allowed == 1){
            //βρες τα επιπλέον αρχεία που ανέβηκαν
            $j = 0;
            $extraFileNames = [];
            $protocolElements = explode("/", $secondment->protocol_date);
            $protocolYear = $protocolElements[2];
            foreach($fileNames as $serverFileName => $databaseFileName){
                $j++;
                if($j > $lastFileNumber){
                    $extraFileNames[$serverFileName] = $databaseFileName;
                }
            }
            $data = [
                ['name' => 'ProtocolNo', 'contents' => $secondment->protocol_nr],
                ['name' => 'ProtocolYear', 'contents' => $protocolYear],
            ];
            //$fileNames = json_decode($secondment->files_json, true);
            foreach($extraFileNames as $serverFileName => $databaseFileName){
                $data[] = [
                    'name'     => 'Files',
                    'contents' => fopen(storage_path("app/secondments/$serverFileName"), 'r'),
                ];
            }
            $extraFilesToProtocol = $this->sendAttachmentsToProtocol($data);
            if($extraFilesToProtocol == false){
                return back()->with('failure', 'Τα αρχεία υποβλήθηκαν με επιτυχία. Απέτυχε η αποστολή τους στο Ηλεκτρονικό Πρωτόκολλο του ΠΥΣΠΕ. Παρακαλούμε αναφέρετε οπωσδήποτε το πρόβλημα στο it@dipe.ach.sch.gr.');
            }  
        } // Τέλος της διαδικασίας ανέβασματος επιπλέον αρχείων
        if ($request->wantsJson()) {
            return response()->json(['success' => 'Τα αρχεία ανέβηκαν επιτυχώς.']);
        } else {
            if($extraFilesToProtocol){
                $extraFilesMessage = " και υποβλήθηκαν συμπληρωματικά στην αίτησή σας στο Ηλεκτρονικό Πρωτόκολλο του ΠΥΣΠΕ.";
            } else {
                $extraFilesMessage = ".";
            }
            return back()->with('success', 'Τα αρχεία ανέβηκαν επιτυχώς'.$extraFilesMessage);
        }
    }

    public function getSchoolChoices($klados, $org_eae){

        switch($klados){
            case "ΠΕ60":
            case "ΠΕ60.50":
                if($org_eae == 0){
                    $schools = School::where('primary', '=', 0)->where('special_needs', '=', 0)->
                    where('public', '=', 1)->orderBy('municipality_id', 'asc')->orderBy('name', 'asc')->get();          
                } else {
                    $schools = School::where('primary', '=', 0)->where(function($query) {
                        $query->where('special_needs', '=', 1)
                              ->orWhere('has_integration_section', '=', 1); // replace 'other_condition' with your actual column name
                    })->
                    where('public', '=', 1)->orderBy('municipality_id', 'asc')->orderBy('name', 'asc')->get();                
                }
            break;
            default:
                if($org_eae == 0){
                    $schools = School::where('primary', '=', 1)->where('special_needs', '=', 0)->
                    where('public', '=', 1)->orderBy('municipality_id', 'asc')->orderBy('name', 'asc')->get();                
                } else {
                    $schools = School::where('primary', '=', 1)->where(function($query) {
                        $query->where('special_needs', '=', 1)
                              ->orWhere('has_integration_section', '=', 1); // replace 'other_condition' with your actual column name
                    })->
                    where('public', '=', 1)->orderBy('municipality_id', 'asc')->orderBy('name', 'asc')->get();                
                }   
            break;
            
        }
        return $schools;
    }
    //Create pdf file and store it in storage/app/secondments
    public function createPDF(Secondment $secondment, $selectionOrder){
        $secondment->teacher->afm;
        $pdf = PDF::loadView('microapps.secondments.toPDF', ['secondment' => $secondment, 'selectionOrder' => $selectionOrder]);
        Storage::makeDirectory('secondments');
        $path = storage_path("app/secondments/{$secondment->teacher->afm}_application_form.pdf");
        $pdf->save($path);
        //$path = $pdf->storeAs('secondments', 'application.pdf', 'local');
        return;
             //return $pdf->download('secondment.pdf');
    }
    //Μέθοδος που στέλνει τα απαραίτητα στοιχεία κάθε φορά στο Πρωτόκολλο
    public function sendSecondmentToProtocol(Secondment $secondment, $criteriaOrPreferences){

        if($secondment->teacher->organiki_type == "App\Models\School"){
            $organicDirectorateCode = '9906101';
            $organicSchoolCode = $secondment->teacher->organiki->code;
        } else {
            $organicDirectorateCode = $secondment->teacher->organiki->code;
            $organicSchoolCode = '';
        }
        $data = [
            ['name' => 'Afm', 'contents' => $secondment->teacher->afm ],
            ['name' => 'StatementOfDeclaration', 'contents' => ($secondment->statement_of_declaration == 1? 'true' : 'false')],
            ['name' => 'ApplicationForReposition', 'contents' => ($secondment->application_for_reposition == 1? 'true' : 'false')],
            ['name' => 'SpecialCategory', 'contents' => ($secondment->special_category == 1? 'true' : 'false')],
            ['name' => 'HealthIssues', 'contents' => $secondment->health_issues],
            ['name' => 'ParentsHealthIssues', 'contents' => $secondment->parents_health_issues],
            ['name' => 'SiblingsHealthIssues', 'contents' => $secondment->siblings_health_issues],
            ['name' => 'IVF', 'contents' => ($secondment->IVF == 1? 'true' : 'false')],
            ['name' => 'PostGraduateStudies', 'contents' => ($secondment->post_graduate_studies == 1? 'true' : 'false')],
            ['name' => 'MaritalStatus', 'contents' => $secondment->marital_status],
            ['name' => 'NrOfChildren', 'contents' => $secondment->nr_of_children],
            ['name' => 'OrganicDirectorateCode', 'contents' => $organicDirectorateCode],
            ['name' => 'OrganicSchoolCode', 'contents' => $organicSchoolCode],
            [
                'name'     => 'Files',
                'contents' => fopen(storage_path("app/secondments/{$secondment->teacher->afm}_application_form.pdf"), 'r')
            ],
        ];
        if($secondment->files_json){
            $fileNames = json_decode($secondment->files_json, true);
            foreach($fileNames as $serverFileName => $databaseFileName){
                $data[] = [
                    'name'     => 'Files',
                    'contents' => fopen(storage_path("app/secondments/$serverFileName"), 'r'),
                ];
            }
        }
                                
        if($secondment->teacher->work_experience){
            $data[] = ['name' => 'WorkExperienceYears', 'contents' => $secondment->teacher->work_experience->years];
            $data[] = ['name' => 'WorkExperienceMonths', 'contents' => $secondment->teacher->work_experience->months];
            $data[] = ['name' => 'WorkExperienceDays', 'contents' => $secondment->teacher->work_experience->days];
        }
        if($secondment->preferences_comments)
           $data[] = ['name' => 'PreferencesComments', 'contents' => $secondment->preferences_comments];
        if($secondment->comments)
           $data[] = ['name' => 'Comments', 'contents' => $secondment->comments];
        if($secondment->parents_municipality)
           $data[] = ['name' => 'ParentsMunicipality', 'contents' => $secondment->parents_municipality];
        if($secondment->siblings_municipality)
           $data[] = ['name' => 'SiblingsMunicipality', 'contents' => $secondment->siblings_municipality];
        if($secondment->studies_municipality)
           $data[] = ['name' => 'StudiesMunicipality', 'contents' => $secondment->studies_municipality];
        if($secondment->civil_status_municipality)
           $data[] = ['name' => 'CivilStatusMunicipality', 'contents' => $secondment->civil_status_municipality];
        if($secondment->living_municipality)
           $data[] = ['name' => 'LivingMunicipality', 'contents' => $secondment->living_municipality];
        if($secondment->partner_working_municipality)
           $data[] = ['name' => 'PartnerWorkingMunicipality', 'contents' => $secondment->partner_working_municipality];
        
        $client = new Client();
        $response = $client->request('POST', env('E_DIRECTORATE').'/application/secondment', [
            'headers' => [
                'X-API-Key' => env('API_KEY'),
            ],
            'multipart' => $data,
        ]);
        // Get the response body
        $status = $response->getStatusCode();
        $body = $response->getBody();
        if($status != 200){
            dd($body);
            return false;
        } else {
            return $body;
        }
    }

    public function sendAttachmentsToProtocol($data){
        $client = new Client();
        $response = $client->request('POST', env('E_DIRECTORATE').'/application/attachments', [
            'headers' => [
                'X-API-Key' => env('API_KEY'),
            ],
            'multipart' => $data,
        ]);
        // Get the response body
        $status = $response->getStatusCode();
        $body = $response->getBody();
        if($status != 200){
            return false;
        } else {
            return $body;
        }
    }

    public function sendSchoolsToProtocol(Secondment $secondment){
        $data = [];
        $protocolElements = explode("/", $secondment->protocol_date);
        $protocolYear = $protocolElements[2];
        $data = [
            ['name' => 'ProtocolNo', 'contents' => $secondment->protocol_nr],
            ['name' => 'ProtocolYear', 'contents' => $protocolYear],
        ];
        if($secondment->preferences_json){
            $selectedCodes = json_decode($secondment->preferences_json);
            foreach($selectedCodes as $schoolCode){
                $data[] = [
                    'name'     => 'Schools',
                    'contents' => $schoolCode,
                ];
            }
        }
        $data[] = [
            'name'     => 'File',
            'contents' => fopen(storage_path("app/secondments/{$secondment->teacher->afm}_application_form.pdf"), 'r')
        ];

        $client = new Client();
        $response = $client->request('POST', env('E_DIRECTORATE').'/application/schools', [
            'headers' => [
                'X-API-Key' => env('API_KEY'),
            ],
            'multipart' => $data,
        ]);
        // Get the response body
        $status = $response->getStatusCode();
        $body = $response->getBody();
        if($status != 200){
            return false;
        } else {
            return $body;
        }

    }

    public function sendRevokeToProtocol($data){
        $client = new Client();
        $response = $client->request('POST', env('E_DIRECTORATE').'/application/revoke', [
            'headers' => [
                'X-API-Key' => env('API_KEY'),
            ],
            'multipart' => $data,
        ]);
        // Get the response body
        $status = $response->getStatusCode();
        $body = $response->getBody();
        if($status != 200){
            return false;
        } else {
            return $body;
        }
    }

    public function download_file($file, $download_file_name = null){
        $username = Auth::check() ? Auth::user()->username : Auth::guard('teacher')->user()->afm;
        $directory = "secondments";
        $fileHandler = new FilesController();
        $download = $fileHandler->download_file($directory, $file, 'local', $download_file_name);
        if($download->getStatusCode() == 500){
            Log::channel('files')->error($username." File $file failed to download");
            return back()->with('failure', 'Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($username." File $file successfully downloaded");
        return $download;
    }

    public function revoke(Secondment $secondment){
        $protocolElements = explode("/", $secondment->protocol_date);
        $protocolYear = $protocolElements[2];
        try{
            $secondment->revoked = 1;
            $data = [
                ['name' => 'ProtocolNo', 'contents' => $secondment->protocol_nr],
                ['name' => 'ProtocolYear', 'contents' => $protocolYear],
            ];
            $protocolRevoke = $this->sendRevokeToProtocol($data);

            if($protocolRevoke){
                $secondment->save();
                // return back()->with('success', 'Η αίτηση ανακλήθηκε και ακυρώθηκε από το Ηλεκτρονικό Πρωτόκολλο του ΠΥΣΠΕ Αχαΐας.');
            } else {
                return back()->with('failure', 'Αποτυχία ανάκλησης αίτησης. Δοκιμάστε ξανά.');
            }
                
        } catch(\Exception $e) {
            //dd($e->getMessage());
            return back()->with('failure', 'Αποτυχία ανάκλησης αίτησης. Δοκιμάστε ξανά.');
        }
        return redirect()->route('secondments.create')->with('success', 'Η αίτηση ανακλήθηκε επιτυχώς και διαγράφηκε από το Ηλεκτρονικό Πρωτόκολλο του ΠΥΣΠΕ');
    }

    public function allow_extra_files(Secondment $secondment){
        if($secondment->criteria_submitted == 1){
            if($secondment->extra_files_allowed == 1){
                $secondment->extra_files_allowed = 0;
                $secondment->save();
                return response()->json(['success' => 'Δεν επιτρέπονται επιπλέον αρχεία']);
            } else {
                $secondment->extra_files_allowed = 1;
                $secondment->save();
                return response()->json(['success' => 'Επιτρέπονται επιπλέον αρχεία']);
            } 
            
        } else {
            return response()->json(['failure' => 'Δεν έχει υποβληθεί οριστικά η αίτηση.']);
        }
        
    }

}
