<?php

namespace App\Http\Controllers\microapps;

use Throwable;
use App\Models\School;
use App\Models\Microapp;
use App\Models\MicroappUser;
use Illuminate\Http\Request;
use App\Models\microapps\Outing;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\microapps\OutingSection;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OutingsController extends Controller
{
    //
    private $microapp;

    public function __construct(){
        $this->middleware('auth')->only(['index']);
        $this->middleware('isSchool')->only(['create', 'store', 'send_delete_request']);
        $this->middleware('canUpdateOuting')->only(['edit', 'update']);
        $this->microapp = Microapp::where('url', '/outings')->first();
    }

    public function action(){
        // return view('microapps.outings.action', ['appname' => 'outings']);
        dd('action');
    }

    public function actions(){
        // return view('microapps.outings.action', ['appname' => 'outings']);
        dd('actions');
    }

    public function index(){
        // ini_set('memory_limit', '256M');
        return view('microapps.outings.index', ['appname' => 'outings']);
    }

    public function create(){
        return view('microapps.outings.create', ['appname' => 'outings']);
    }

    public function edit(Outing $outing){
        return view('microapps.outings.edit', compact('outing'));
    }

    public function store(Request $request){

        //dd($request->all());
        $school = Auth::guard('school')->user();
    
        $rule = [
            'record_file' => 'mimetypes:application/pdf'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου (Επιτρεπτός τύπος: pdf)');
        }
    
        $directory = 'outings';
        $file = $request->file('record_file');
    
        $outing_type = $request->input('type');
        $outing_destination = $request->input('destination');
        $outing_record = $request->input('record');
        $outing_file = $file->getClientOriginalName();
        $outing_date = $request->input('outing_date');

        $action_ids = explode(',', $request->input('action_id')); // Convert comma seperated to array
    
        try {
            $new_outing = Outing::create([
                'school_id' => $school->id,
                'outingtype_id' => $outing_type,
                'outing_date' => $outing_date,
                'destination' => $outing_destination,
                'record' => $outing_record,
                'file' => $outing_file,
                'checked' => 0
            ]);
            
            // Attach actions via pivot table
            if (!empty($action_ids)) {
                $new_outing->actions()->attach($action_ids);
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            Log::channel('throwable_db')->error($school->name . ' create outing error ' . $e->getMessage());
            return back()->with('failure', 'Δεν έγινε η καταχώρηση της εκδρομής, προσπαθήστε ξανά');
        }
    
        // Sections
        try {
            foreach ($request->all() as $key => $value) {
                if (substr($key, 0, 7) == 'section') {
                    OutingSection::create([
                        'outing_id' => $new_outing->id,
                        'section_id' => $value
                    ]);
                }
            }
        } catch (Throwable $e) {
            Log::channel('throwable_db')->error($school->name . ' match Section-Outing error ' . $e->getMessage());
            return back()->with('warning', 'Δεν έγινε η καταχώρηση των τμημάτων και του πρακτικού στην εκδρομή, μπορείτε να την επεξεργαστείτε και να προσπαθήσετε να τα εισάγετε ξανά');
        }
    
        // Upload file
        try {
            $file->storeAs($directory, $school->code . '_' . $new_outing->id . '_' . $file->getClientOriginalName(), 'local');
        } catch (Throwable $e) {
            Log::channel('stakeholders_microapps')->error($school->name . " outing file upload error " . $e->getMessage());
            return back()->with('warning', 'Δεν ανέβηκε το αρχείο στην εκδρομή, μπορείτε να την επεξεργαστείτε και να προσπαθήσετε να το ανεβάσετε ξανά');
        }
    
        return back()->with('success', 'Η εκδρομή καταχωρίστηκε επιτυχώς');
    }
    
    public function download_file(Request $request, Outing $outing){
        $outings_microapp_id = $this->microapp->id;
        if((Auth::check() && (Auth::user()->microapps->where('microapp_id', $outings_microapp_id)->count() or Auth::user()->isAdmin())) || (Auth::guard('school')->check() && Auth::guard('school')->user()->id == $outing->school->id)){
            $file = 'outings/'.$outing->school->code.'_'.$outing->id.'_'.$outing->file;
            $response = Storage::disk('local')->download($file, $outing->file);  
            ob_end_clean();
            try{
                return $response;
            }
            catch(Throwable $e){
            
            return back()->with('failure', 'Δεν ήταν δυνατή η λήψη του αρχείου, προσπαθήστε ξανά');    
            }
        }
        abort(403, 'Unauthorized action.');
    }

    public function destroy(Request $request, Outing $outing){
        $outings_microapp_id = $this->microapp->id;
        if((Auth::check() && (Auth::user()->microapps->where('microapp_id', $outings_microapp_id)->count() or Auth::user()->isAdmin())) || (Auth::guard('school')->check() && Auth::guard('school')->user()->id == $outing->school->id)){    
            $file = 'outings/'.$outing->school->code.'_'.$outing->id.'_'.$outing->file;
            $outing->delete();
            try{
                Storage::disk('local')->delete($file);
            }
            catch(Throwable $e){
        
            }
            if(Auth::guard('school')->check())
                return back()->with('success','Η εκδρομή διαγράφηκε');
            else if (Auth::check())
                return response()->json(['message' => 'Outing deleted '.$outing->id]);
        }
        abort(403, 'Unauthorized action.');
    }

    public function check_outing(Request $request, Outing $outing){
        $outings_microapp_id = Microapp::where('url', '/outings')->first()->id;
        if((Auth::check() && (Auth::user()->microapps->where('microapp_id', $outings_microapp_id)->count() or Auth::user()->isAdmin()))){
            if($request->input('checked')=='true')
                $outing->checked = 1;
            else
                $outing->checked = 0;
            $outing->save();

            return response()->json(['message' => 'Outing updated successfully: '.$outing->checked]);
        }
        abort(403, 'Unauthorized action.');
    }

    public function update(Request $request, Outing $outing){
        $rule = [
            'record_file' => 'mimetypes:application/pdf'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου (Επιτρεπτός τύπος: pdf)');
        }
    
        $outing->outingtype_id = $request->input('type');
        $outing->destination = $request->input('destination');
        $outing->record = $request->input('record');
        $outing->checked = 0;
    
        if ($request->file('record_file')) {
            $old_file = 'outings/' . $outing->school->code . '_' . $outing->id . '_' . $outing->file;
    
            try {
                Storage::disk('local')->delete($old_file);
            } catch (Throwable $e) {
                Log::channel('stakeholders_microapps')->error(Auth::guard('school')->user()->name . " outing file delete error " . $e->getMessage());
            }
    
            $file = $request->file('record_file');
            $directory = 'outings';
    
            try {
                $file->storeAs($directory, $outing->school->code . '_' . $outing->id . '_' . $file->getClientOriginalName(), 'local');
                $outing->file = $file->getClientOriginalName();
            } catch (Throwable $e) {
                session(['warning' => 'Το νέο αρχείο της εκδρομής δεν αποθηκεύτηκε']);
            }
        }
    
        if ($request->filled('outing_date')) {
            $outing->outing_date = $request->input('outing_date');
        }
    
        // Sync actions
        //$action_ids = $request->input('action_ids', []);
        $action_ids = explode(',', $request->input('action_id')); // Convert comma seperated to array
        $outing->actions()->sync($action_ids);
    
        // Clear old sections
        foreach ($outing->sections as $out_sect) {
            $out_sect->delete();
        }
    
        // Add new sections
        foreach ($request->all() as $key => $value) {
            try {
                if (substr($key, 0, 7) == 'section') {
                    OutingSection::create([
                        'outing_id' => $outing->id,
                        'section_id' => $value
                    ]);
                }
            } catch (Throwable $e) {
                $name = Section::find($value)->name;
                Log::channel('stakeholders_microapps')->error(Auth::guard('school')->user()->name . " add section $value to outing error " . $e->getMessage());
                session(['error' => "Το τμήμα $name δεν αποθηκεύτηκε στην εκδρομή"]);
            }
        }
    
        $outing->save();
    
        return redirect()->route('outings.create')->with('success', 'Τα στοιχεία της εκδρομής ενημερώθηκαν');
    }
    

    public function update_outing_action(Request $request){
        Log::info('Update outing action: '.json_encode($request->all()));
        Log::info('Update outing action: '.json_encode($request->outing_id));
       
        try {
            $request->validate([
                'outing_id' => 'required|exists:outings,id',
                'action_ids' => 'required|exists:actions,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Errors:', $e->errors());
            throw $e; // Re-throw the exception if needed
        }
        try{
            
            $outing = Outing::findOrFail($request->outing_id);
            $outing->actions()->sync($request->action_ids);
            $outing->save();
        } catch(Throwable $e){
            return response()->json(['fail' => false, 'message' => 'Failed: '.$e->getMessage()]);
        }
        
    
        return response()->json(['success' => true, 'message' => 'Outing updated successfully!']);
    }

    public function count_sections(Outing $outing){
        // $school = $outing->school;
        $out_sections = $outing->sections;
        $counting = array();
        foreach($out_sections as $section){
            $sec_count = 0;
            $sec_name = $section->section->name;
            $sec_id = $section->section->id;
            $sec_outings = $section->section->outings;
            foreach($sec_outings as $sec_outing){
                if($sec_outing->outing->outingtype_id != 4)
                    $sec_count++;
            } 
            $counting[$sec_name] = $sec_count;
            
        }
        return response()->json(['sections' => json_encode($counting)]);
    }

    public function send_delete_request(Request $request, Outing $outing){
        if($outing->school_id != Auth::guard('school')->user()->id)
            return back()->with('failure', 'Δεν έχετε δικαίωμα να ζητήσετε τη διαγραφή της εκδρομής');
        $microapp = Microapp::where('url', '/outings')->first();
        $string = '';
        foreach(MicroappUser::where('microapp_id', $this->microapp->id)->get() as $user){
            $user->user->notify(new UserNotification('Ο χρήστης '.Auth::guard('school')->user()->name.' ζήτησε τη διαγραφή της εκδρομής '.$outing->id.' με αιτιολογία: '.$request->input('delete_request'), 'Διαγραφή εκδρομής '.$outing->id));
        }
        return back()->with('success', "Το αίτημα διαγραφής της εκδρομής στάλθηκε στο Τμήμα Εκπαιδευτικών Θεμάτων της Διεύθυνσης Π.Ε.");
    }
}
