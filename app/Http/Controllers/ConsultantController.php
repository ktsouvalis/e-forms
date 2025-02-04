<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models\Consultant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultantController extends Controller
{
    /// From here ////
    public function index()
    {
        $consultants = Consultant::all();
        return view('consultants.index', compact('consultants'));
    }

    public function create()
    {
        return view('consultants.create');
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
    //
    public function login($md5){ 
        $consultant = Consultant::where('md5', $md5)->firstOrFail();
        //logs the teacher in using the 'teacher' guard
        Auth::guard('consultant')->login($consultant);
        session()->regenerate();   
        $consultant->save();

        return redirect(url('/index_consultant'))->with('success',"$consultant->name καλωσήρθατε!");
    }

    public function logout(){
        auth()->guard('consultant')->logout();
        return redirect(url('/index_consultant'))->with('success', 'Αποσυνδεθήκατε');
    }

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
