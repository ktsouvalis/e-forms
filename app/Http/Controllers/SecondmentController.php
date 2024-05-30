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
        return view('microapps.secondments.index');
    }
    //Επεξεργασία, προσωρινή αποθήκευση, προεπισκόπηση και οριστική υποβολή αίτησης
    public function update(Secondment $secondment, Request $request){
        //dd($request->input());
        if($request->input()['criteriaOrPreferences'] == 1){        //Αποθήκευση μοριοδοτούμενων κριτηρίων
            try{
                $secondment->update($request->input());
                return back()->with('success', 'Τα στοιχεία αποθηκεύτηκαν.');
            } catch(\Exception $e) {
                //dd($e->getMessage());
                return back()->with('failure', 'Αποτυχία αποθήκευσης αίτησης.');
            }
        } else {                                                    //Αποθήκευση προτιμήσεων, οριστικοποίηση και αποστολή
            //Αν έχει συμπληρώσει επιλογές στον πίνακα προτιμήσεων πάρε τις προτιμήσεις με τη σειρά που έχουν συμπληρωθεί
            if(isset($request->input()['schools-select'])){
                $secondment->preferences_json = $request->input()['selectionOrder'];//φέρε την πραγματική σειρά που έχει συμπληρωθεί με javascript κάθε φορά που αλλάζει σχολεία
            } else {
                $secondment->preferences_json = null;
            }
            if($request->action == 'preview'){//Αν ζητήσει προεπισκόπηση
                return view('microapps.secondments.toPDF', ['secondment' => $secondment, 'selectionOrder' => $request->input()['selectionOrder']]);
            }
            //Δε ζητάει προεπισκόπηση - ζητάει αποθήκευση ή οριστική υποβολή - Αποθήκευσε τα στοιχεία
            try{
                $secondment->update($request->input());
                if($request->action == 'save'){//Αν ζητάει μόνο αποθήκευση επέστρεψε με μήνυμα επιτυχίας
                    return back()->with('success', 'Επιτυχής αποθήκευση αίτησης.');
                }
                //Αν ζητάει οριστική υποβολή αλλά έχει κενές τις προτιμήσεις, έχοντας κρατήσει τις τιμές, επέστρεψε με μήνυμα αποτυχίας
                if($request->action == 'submit' && $request->input()['selectionOrder']=="[]"){
                    return back()->with('failure', 'Δε μπορεί να πραγματοποιηθεί οριστική υποβολή χωρίς προτιμήσεις. Αν επιθυμείτε να ακυρώσετε την αίτησή σας παρακαλούμε αφήστε τη σε κατάσταση προσωρινής αποθήκευσης και δε θα ληφθεί υπόψη.');
                }
                if($request->action == 'update'){
                    return back()->with('success', 'Επιτυχής αποθήκευση αίτησης.');
                }
            } catch(\Exception $e) {
                //dd($e->getMessage());
                return back()->with('failure', 'Αποτυχία αποθήκευσης αίτησης.');
            }
            //Ζητάει οριστική υποβολή και έχει συμπληρώσει τις προτιμήσεις
            //Δημιούργησε το pdf
            try{
                $this->createPDF($secondment, $request->input()['selectionOrder']);
            } catch(\Exception $e) {
                //dd($e->getMessage());
                return back()->with('failure', 'Η αίτηση αποθηκεύτηκε αλλά απέτυχε η δημιουργία του pdf αρχείου. Προσπαθήστε ξανά. Σε περίπτωση προβλήματος παρακαλούμε για την αποστολή mail στο it@dipe.ach.sch.gr.');
            }
            //Στείλε την αίτηση στο πρωτόκολλο
            $protocol_message = $this->sendToProtocol($secondment);
            if($protocol_message == false){
                return back()->with('failure', 'Η αίτηση αποθηκεύτηκε αλλά απέτυχε η αποστολή στο πρωτόκολλο. Προσπαθήστε ξανά. Σε περίπτωση προβλήματος παρακαλούμε για την αποστολή mail στο it@dipe.ach.sch.gr.');
            }
            //Ανανέωσε το submitted
            try{
                $protocol_message = explode(" - ", $protocol_message);
                $secondment->protocol_nr = $protocol_message[0];
                $secondment->protocol_date = $protocol_message[1];
                $secondment->submitted = 1;
                $secondment->update();
            } catch(\Exception $e) {
                return back()->with('failure', $e->getMessage());
                //return back()->with('failure', 'Η αίτηση πρωτοκολλήθηκε αλλά απέτυχε η οριστικοποίησή της. Επικοινωνήστε άμεσα με το Τμήμα Πληροφορικής 2610229262 it@dipe.ach.sch.gr.');
            }
            return back()->with('success', 'Επιτυχής οριστικοποίηση αίτησης. Η αίτησή σας πρωτοκολλήθηκε αυτόματα στο Ηλεκτρονικό Πρωτόκολλο του ΠΥΣΠΕ Αχαΐας με αρ. πρωτ.: '. $protocol_message[0] . '-' . $protocol_message[1]. '.');
        }  
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
                $secondment = new Secondment();
                $secondment->teacher_id = $teacher->id;
                $secondment->statement_of_declaration = true;
                $secondment->application_for_reposition = (isset($request->application_for_reposition)? true : false);
                $secondment->save();
                return redirect(route('secondments.edit', ['secondment' => $secondment->id]))->with('success',"Επιτυχής αποθήκευση αίτησης. Μπορείτε να προχωρήσετε σε δήλωση μοριοδοτούμενων κριτηρίων.");
            } catch(\Exception $e) {
                dd($e);
                return back()->with('failure', 'Αποτυχία αποθήκευσης αίτησης.');
            }
        }
        
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
        $request->validate([
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        $files = $request->file('files');
        $fileNames = [];
        //Βρες πόσα αρχεία έχει ήδη ανεβάσει
        if($secondment->files_json){
            $fileNames = json_decode($secondment->files_json, true);
            end($fileNames);
            $lastServerFileName = key($fileNames);
            $underScorePosition = strpos($lastServerFileName, '_');
            $filesCount = substr($lastServerFileName, $underScorePosition + 1, strpos($lastServerFileName, '.') - $underScorePosition -1);
        } else {
            $filesCount = 0;
        }
        $lastFileNumber = $filesCount; // κράτα τον αριθμό του τελευταίου αρχείου για την περίπτωση που θα ανεβάσει επιπλέον αρχεία
        $directory = "secondments";
        $teacherAfm = Auth::guard('teacher')->user()->afm;
        foreach($files as $file){
            $filesCount++;
            $serverFileName = $teacherAfm."_".$filesCount.".".$file->getClientOriginalExtension();
            $fileNames[$serverFileName] = $file->getClientOriginalName();
            $fileHandler = new FilesController();
            
            $uploaded = $fileHandler->upload_file($directory, $file, 'local', $serverFileName);
            
            if($uploaded->getStatusCode() == 500){
                Log::channel('files')->error($teacherAfm." Files failed to upload");
                return back()->with('failure', 'Αποτυχία στην υποβολή των αρχείων. Δοκιμάστε ξανά');
            }
        }
        $secondment->files_json = json_encode($fileNames);
        try{
            $secondment->update();
        } catch(\Exception $e) {
            //dd($e->getMessage());
            Log::channel('files')->error($teacherAfm." Files failed to update database field files_json");
            return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων με τα ονόματα των αρχείων. Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($teacherAfm." Files successfully uploaded");
        if($secondment->submitted == 1 && $secondment->extra_files_allowed == 1){
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
            $client = new Client();
            $response = $client->request('POST', 'http://10.35.249.138/eprotocolapi/api/application/attachments', [
                'headers' => [
                    'X-API-Key' => 'mysecretapikey',
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
        if ($request->wantsJson()) {
        return response()->json(['success' => 'Files uploaded successfully.']);
        } else {
        return back()->with('success', 'Τα αρχεία ανέβηκαν επιτυχώς');
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

    public function sendToProtocol(Secondment $secondment){
        
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
        if($secondment->preferences_json){
            $selectedCodes = json_decode($secondment->preferences_json);
            foreach($selectedCodes as $schoolCode){
                $data[] = [
                    'name'     => 'Schools',
                    'contents' => $schoolCode,
                ];
            }
        }
        $client = new Client();
        $response = $client->request('POST', 'http://10.35.249.138/eprotocolapi/api/application/secondment', [
            'headers' => [
                'X-API-Key' => 'mysecretapikey',
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
            $client = new Client();
            $response = $client->request('POST', 'http://10.35.249.138/eprotocolapi/api/application/revoke', [
                'headers' => [
                    'X-API-Key' => 'mysecretapikey',
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
            $secondment->save();
        } catch(\Exception $e) {
            dd($e->getMessage());
            return back()->with('failure', 'Αποτυχία ανάκλησης αίτησης. Δοκιμάστε ξανά.');
        }
        return redirect()->route('secondments.create')->with('success', 'Η αίτηση ανακλήθηκε επιτυχώς και διαγράφηκε από το Ηλεκτρονικό Πρωτόκολλο του ΠΥΣΠΕ');
    }

    public function allow_extra_files(Secondment $secondment){
        if($secondment->submitted == 1){
            if($secondment->extra_files_allowed == 1){
                $secondment->extra_files_allowed = 0;
                $secondment->update();
                return response()->json(['success' => 'Δεν επιτρέπονται επιπλέον αρχεία']);
            } else {
                $secondment->extra_files_allowed = 1;
                $secondment->update();
                return response()->json(['success' => 'Επιτρέπονται επιπλέον αρχεία']);
            } 
            
        } else {
            return response()->json(['failure' => 'Δεν έχει υποβληθεί οριστικά η αίτηση.']);
        }
        
    }

}
