<?php

namespace App\Http\Controllers\microapps;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function __construct(){
        
    }
    public function index()
    {
        if($isTeacher)
            return view('microapps.evaluation.index');
        if($isConsultant)
            return view('microapps.evaluation.index');
    }

    public function create()
    {   
        if(Auth::guard('teacher')->check()){
            dd(Auth::guard('teacher')->user());
            return view('microapps.evaluation.create-director');
        }
            
        else if(Auth::guard('consultant')->check()){
            dd(Auth::guard('teacher')->user());
            return view('microapps.evaluation.create-consultant');
        }
            
        abort(403, 'Unauthorized action.');
        
    }

    public function getEvaluatorData($afm, $whoIs){
        if($whoIs == 'isTeacher') $api_path = '/evaluation/directorcurrent';
        if($whoIs == 'isConsultant') $api_path = '/evaluation/consultantcurrent';
        if(!$api_path) dd('error');
        $client = new Client([
            'debug' => fopen(\storage_path('logs/guzzle-debug.log'), 'w')
        ]);
        $response = $client->request('GET', env('E_DIRECTORATE').$api_path, [
            'headers' => [
                'X-API-Key' => env('API_KEY'),
            ],
            'query' => [
                'afm' => $afm
            ]
        ]);
        // Get the response body
        $status = $response->getStatusCode();
        $contents = $response->getBody()->getContents();
        if($status != 200){
            //dd($body);
            return false;
        } else {
            return $contents;
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mail' => 'required|email|unique:consultants,mail',
            'afm' => 'required|digits:9|unique:consultants,afm,',
            'am' => 'required|digits:6|unique:consultants,am,',
            'surname' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'klados' => 'required|string|max:255',
        ]);
        //create md5 hash
        $md5 = md5($validated['afm'].$validated['am']);
        $validated['md5'] = $md5;

        Consultant::create($validated);   

        return redirect()->route('consultants.index')->with('success', 'Ο Σύμβουλος Εκπαίδευσης δημιουργήθηκε.');
    }

    public function show(Consultant $consultant)
    {
        return view('consultants.show', compact('consultant'));
    }

    public function edit(Consultant $consultant)
    {
        return view('consultants.edit', compact('consultant'));
    }

    public function update(Request $request, Consultant $consultant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mail' => 'required|email|unique:consultants,mail,' . $consultant->id,
            'afm' => 'required|digits:9|unique:consultants,afm,' . $consultant->id,
            'am' => 'required|digits:6|unique:consultants,am,' . $consultant->id,
            'surname' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'klados' => 'required|string|max:255',
        ]);

        $consultant->update($validated);
        // IF is_supervisor is checked and this is not the current supervisor, 
        // FIRST update current supervisor consultant to 0 
        // THEN set this consultant to 1
        if($request->isSupervisor == "on"){
            $supervisorConsultant = Consultant::where('is_supervisor', 1)->first();
            
            if($consultant->id != $supervisorConsultant->id){
                $supervisorConsultant->is_supervisor = 0;
                $supervisorConsultant->save();
                $consultant->is_supervisor = 1;
                $consultant->save();
            }
        }
        return redirect()->route('consultants.index')->with('success', 'Τα στοιχεία ενημερώθηκαν.');
    }

    public function destroy(Consultant $consultant)
    {
        $consultant->delete();
        return redirect()->route('consultants.index')->with('success', 'Ο Σύμβουλος διαγράφηκε.');
    }
    /// Up to here ////

    public static function getEvaluationData($afm){
        $client = new Client();
        $response = $client->request('GET', env('E_DIRECTORATE').'/evaluation/consultantdata?afm='.$afm, [
            'headers' => [
                'X-API-Key' => env('API_KEY'),
            ],
        ]);
        // Get the response body
        $status = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        if($status != 200){
            return false;
        } else {
            return $body;
        }
    }
}
