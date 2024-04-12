<?php

namespace App\Http\Controllers;
use PDF;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\microapps\Secondment;
use Illuminate\Support\Facades\Auth;
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
        try{
            $secondment->update($request->input());
            return back()->with('success', 'Επιτυχής αποθήκευση αίτησης.');
        } catch(\Exception $e) {
            return back()->with('failure', 'Αποτυχία αποθήκευσης αίτησης.');
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

    public function createPDF(Secondment $secondment)
    {
        $pdf = PDF::loadView('microapps.secondments.pdf', ['secondment' => $secondment]);
        return $pdf->download('secondment.pdf');
    }

}
