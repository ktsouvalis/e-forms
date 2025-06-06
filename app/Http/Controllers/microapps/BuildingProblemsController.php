<?php

namespace App\Http\Controllers\microapps;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FilesController;
use App\Models\microapps\BuildingProblems;

class BuildingProblemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('microapps.building_problems.index', ['appname' => 'building_problems']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('microapps.building_problems.create', ['appname' => 'building_problems']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd('store', $request->all());
        $school = Auth::guard('school')->user();

        // Validate the request
        $validated = $request->validate([
            'severity' => 'required|integer|min:0|max:5',
            'comments' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);
        
        // Find or create a building problem record
        $buildingProblem = BuildingProblems::firstOrNew([
            'school_id' => $school->id,
        ]);
        
        // Update the record with the new data
        $buildingProblem->fill([
            'severity' => $validated['severity'],
            'comments' => $validated['comments'],
        ]);
        
        // Handle file uploads if present
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $fileNames = [];
            //Βρες πόσα αρχεία έχει ήδη ανεβάσει
            if($buildingProblem->files_json){ // Αν έχει ανεβάσει ήδη, βρες τον αριθμό του τελευταίου αρχείου από το όνομά του
                $fileNames = json_decode($buildingProblem->files_json, true);
                end($fileNames);
                $lastServerFileName = key($fileNames);
                $underScorePosition = strpos($lastServerFileName, '_');// βρες τον αριθμό που περιλαμβάνεται στο όνομα του τελευταίου αρχείου μετά το _
                $filesCount = substr($lastServerFileName, $underScorePosition + 1, strpos($lastServerFileName, '.') - $underScorePosition -1);
            
            } else { //Αν δεν έχει ανεβάσει ακόμη αρχεία, βάλε τον αριθμό 0
                $filesCount = 0;
            }
            $lastFileNumber = $filesCount; // κράτα τον αριθμό του τελευταίου αρχείου για την περίπτωση που θα ανεβάσει επιπλέον αρχεία
            $directory = "building_problems"; // Ο φάκελος στον οποίο θα αποθηκευτούν τα αρχεία
            $schoolCode = Auth::guard('school')->user()->code;
            foreach($files as $file){ // Για κάθε αρχείο που ανεβάζεις
                $filesCount++;
                $serverFileName = $schoolCode."_".$filesCount.".".$file->getClientOriginalExtension();
                $fileNames[$serverFileName] = $file->getClientOriginalName();//πρόσθεσε στον πίνακα το όνομα του αρχείου που θα ανεβάσεις
                // dd($fileNames);
                $fileHandler = new FilesController();
                $uploaded = $fileHandler->upload_file($directory, $file, 'local', $serverFileName);
                
                if($uploaded->getStatusCode() == 500){
                    Log::channel('files')->error($teacherAfm." Files failed to upload");
                    return back()->with('failure', 'Αποτυχία στην υποβολή των αρχείων. Δοκιμάστε ξανά');
                }
            }
            $buildingProblem->files_json = json_encode($fileNames);
            try{
                $buildingProblem->save();
            } catch(\Exception $e) {
                //dd($e->getMessage());
                Log::channel('files')->error($teacherAfm." Building Problem Files failed to update database field files_json");
                return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων με τα ονόματα των αρχείων. Δοκιμάστε ξανά');
            }
        }
        // Save the record
        $buildingProblem->save();
        try{
            $protocol_response = $this->sendBuildingProblemsToProtocol($buildingProblem);
            $protocol_response = explode(" - ", $protocol_response);
            $protocol_nr = $protocol_response[0];
            $protocol_date = $protocol_response[1];
            //dd($response);
        } catch (\Exception $e){
            //dd($e->getMessage());
            return back()->with('failure', 'Αποτυχία αποστολής στοιχείων στο πρωτόκολλο της Διεύθυνσης.');
        }
        try{
            $buildingProblem->fill([
                'protocol_nr' => $protocol_nr,
                'protocol_date' => $protocol_date,
            ]);
            $buildingProblem->save();
        } catch (\Exception $e){
            return back()->with('failure', 'Τα στοιχεία αποθηκεύτηκαν και έχουν αποσταλεί στο πρωτόκολλο της Διεύθυνσης. Δεν αποθηκεύτηκε ο αριθμός πρωτοκόλλου. Επικοινωνήστε με το Τμήμα Πληροφορικής της Διεύθυνσης.');
        }
        
        return redirect()->back()->with('success', 'Η αποθήκευση των στοιχείων ολοκληρώθηκε με επιτυχία. Πρωτοκολλήθηκε στο Ηλεκτρονικό Πρωτόκολλο της Διεύθυνσης με αρ. πρωτ. '. $protocol_nr .' - '. $protocol_date);
    }

    //Διαγραφή αρχείου
    public function delete_file(BuildingProblems $buildingProblems, $serverFileName){
        
        $fileHandler = new FilesController();
        $files = json_decode($buildingProblems->files_json, true);
        try{
            $fileHandler->delete_file('building_problems', $serverFileName, 'local');
            $databaseFileName = $files[$serverFileName];
            $key = array_search($databaseFileName, $files);
            if ($key !== false) {
                unset($files[$key]);
            }
            //dd($files);
            $buildingProblems->files_json = json_encode($files);
            $buildingProblems->update();
        } catch(\Exception $e) {
            return back()->with('failure', 'Αποτυχία διαγραφής αρχείου.');
        }
        return back()->with('success', 'Επιτυχής διαγραφή αρχείου: "'.$databaseFileName.'"');
    }

    public function download_file($serverFileName, $databaseFileName = null){
        $schoolCode = Auth::guard('school')->user()->code;
        $directory = "building_problems";
        $fileHandler = new FilesController();
        $download = $fileHandler->download_file($directory, $serverFileName, 'local', $databaseFileName);
        if($download->getStatusCode() == 500){
            Log::channel('files')->error($username." File $databaseFileName failed to download");
            return back()->with('failure', 'Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($schoolCode." File $databaseFileName successfully downloaded");
        return $download;
    }

    private function sendBuildingProblemsToProtocol(BuildingProblems $buildingProblem){

        $data = [
            ['name' => 'SchoolCode', 'contents' => $buildingProblem->school->code ],
            ['name' => 'Severity', 'contents' => $buildingProblem->severity],
        ];
        if($buildingProblem->comments){
               $data[] = ['name' => 'Remarks', 'contents' => $buildingProblem->comments];
            }
        if($buildingProblem->files_json){
            $fileNames = json_decode($buildingProblem->files_json, true);
            foreach($fileNames as $serverFileName => $databaseFileName){
                $data[] = [
                    'name'     => 'Files',
                    'contents' => fopen(storage_path("app/building_problems/$serverFileName"), 'r'),
                ];
            }
        }
                                
        $client = new Client();

        $url = ($buildingProblem->protocol_nr)?env('E_DIRECTORATE').'/BuildingIssues/update': env('E_DIRECTORATE').'/BuildingIssues/new';
    
        $response = $client->request('POST', $url, [
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

    /**
     * Display the specified resource.
     */
    public function show(BuildingProblems $buildingProblems)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BuildingProblems $buildingProblems)
    {
        //
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BuildingProblems $buildingProblems)
    {
        //
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BuildingProblems $buildingProblems)
    {
        //
    }

    public function updload_files(Request $request)
    {
        // Handle file upload logic here
        // Validate and store files, then return a response
        return response()->json(['message' => 'Files uploaded successfully']);
    }
}
