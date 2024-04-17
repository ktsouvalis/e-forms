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
                dd($e->getMessage());
                return back()->with('failure', 'Η αίτηση αποθηκεύτηκε αλλά απέτυχε η δημιουργία του pdf αρχείου. Προσπαθήστε ξανά. Σε περίπτωση προβλήματος παρακαλούμε για την αποστολή mail στο it@dipe.ach.sch.gr.');
            }
            
            //Στείλε την αίτηση στο πρωτόκολλο
            if(!$this->sendToProtocol($secondment)){
                return back()->with('failure', 'Η αίτηση αποθηκεύτηκε αλλά απέτυχε η αποστολή στο πρωτόκολλο. Προσπαθήστε ξανά. Σε περίπτωση προβλήματος παρακαλούμε για την αποστολή mail στο it@dipe.ach.sch.gr.');
            }

            //Ανανέωσε το submitted
            try{
                $secondment->submitted = false;
                $secondment->update();
                return redirect(route('secondments.create'))->with('success', 'Επιτυχής οριστικοποίηση αίτησης.');
            } catch(\Exception $e) {
                return back()->with('failure', 'Η αίτηση πρωτοκολλήθηκε αλλά απέτυχε η οριστικοποίησή της. Επικοινωνήστε άμεσα με το Τμήμα Πληροφορικής 2610229262 it@dipe.ach.sch.gr.');
            }
        }
        
    }

    public function create() 
    {
        if(Auth::guard('teacher')->user()->secondment)
        {
            $secondment = Auth::guard('teacher')->user()->secondment;
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
                return back()->with('failure', 'Αποτυχία αποθήκευσης αίτησης.');
            }
        }
        
    }

    public function getSchoolChoices($klados, $org_eae){

        switch($klados){
            case "ΠΕ60":
                if($org_eae == 0){
                    $schools = School::where('primary', '=', 0)->where('special_needs', '=', 0)->orderBy('municipality_id', 'asc')->orderBy('name', 'asc')->get();          
                } else {
                    $schools = School::where('primary', '=', 0)->where('special_needs', '=', 1)->orderBy('municipality_id', 'asc')->orderBy('name', 'asc')->get();                
                }
            break;
            case "ΠΕ70":
                if($org_eae == 0){
                    $schools = School::where('primary', '=', 1)->where('special_needs', '=', 0)->orderBy('municipality_id', 'asc')->orderBy('name', 'asc')->get();                
                } else {
                    $schools = School::where('primary', '=', 0)->where('special_needs', '=', 1)->orderBy('municipality_id', 'asc')->orderBy('name', 'asc')->get();                
                }   
            break;
            default:
                $schools = School::all();
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
        $data = [
            [
                'name'     => 'Afm',
                'contents' => $secondment->teacher->afm
            ],
            [
                'name'     => 'Files',
                'contents' => fopen(storage_path("app/secondments/{$secondment->teacher->afm}_application_form.pdf"), 'r')
            ],
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
        return true;
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

}
