<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\School;
use GuzzleHttp\Client;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function update(Secondment $secondment, Request $request)
    {
        if(isset($request->input()['schools-select'])){
            $secondment->preferences_json = $request->input()['selectionOrder'];
        } else {
            $secondment->preferences_json = null;
        }
        if($request->action == 'preview'){
            return view('microapps.secondments.toPDF', ['secondment' => $secondment, 'selectionOrder' => $request->input()['selectionOrder']]);
        }
        //Αποθήκευσε τα στοιχεία της αίτησης χωρίς το submitted
        try{
            $secondment->update($request->input());
            //return back()->with('success', 'Επιτυχής αποθήκευση αίτησης.');
        } catch(\Exception $e) {
            return back()->with('failure', 'Αποτυχία αποθήκευσης αίτησης.');
        }
        //Δημιούργησε το pdf
        $this->createPDF($secondment, $request->input()['selectionOrder']);
        //Στείλε την αίτηση στο πρωτόκολλο
       $this->sendToProtocol($secondment);
        //Ανανέωσε το submitted
        $secondment->submitted = true;
        try{
            $secondment->update();
            return redirect(route('secondments.create'))->with('success', 'Επιτυχής οριστικοποίηση αίτησης.');
        } catch(\Exception $e) {
            return back()->with('failure', 'Αποτυχία οριστικοποίησης αίτησης.');
        }
        

    }
    public function create() 
    {
        if(Auth::guard('teacher')->user()->secondment)
        {
            $secondment = Auth::guard('teacher')->user()->secondment;
            return redirect(route('secondments.edit', ['secondment' => $secondment->id, 'phase' => 1]));
        }
        return view('microapps.secondments.create');
    }

    public function edit(Secondment $secondment, Request $request)
    {   if($request->input()){
            $criteriaOrPreferences = $request->input()['phase'];
        } else {
            $criteriaOrPreferences = 1;
        }
        if($criteriaOrPreferences == 1){
            return view('microapps.secondments.edit_criteria', ['secondment' => $secondment]);
        } else if ($criteriaOrPreferences == 2){
            return view('microapps.secondments.edit_preferences', ['secondment' => $secondment]);
        }
    }

    public function store(Request $request)
    {
        //
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
        return;
    }

}
