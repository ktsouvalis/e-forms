<?php

namespace App\Http\Controllers\microapps;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\microapps\ActionPlanning;
use App\Http\Controllers\FilesController;


class ActionPlanningController extends Controller
{
      /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('microapps.action_planning.index', ['appname' => 'action_planning']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $school = Auth::guard('school')->user();
        $appname = 'action_planning';
        
        $microapp = \App\Models\Microapp::where('url', '/' . $appname)->first();

        $plan = $school->actionPlanning; 
        
        // Αν υπάρχει plan, παίρνουμε τον τύπο. Αν όχι, είναι null.
        $selectedCycle = optional($plan)->planning_cycle;
        
        return view('microapps.action_planning.create', compact(
            'school', 'microapp', 'plan', 'appname', 'selectedCycle'
        ));
    }

    public function select_cycle(Request $request)
    {
        $school = Auth::guard('school')->user();
        
        $validated = $request->validate([
            'planning_cycle' => 'required|in:Ετήσιος,Τριμηνιαίος',
        ]);

        // Εύρεση ή δημιουργία της εγγραφής
        $plan = ActionPlanning::firstOrNew(['school_id' => $school->id]);
        
        // Αν δεν έχει ήδη τύπο, τον αποθηκεύουμε
        if (!$plan->planning_cycle) {
            $plan->planning_cycle = $validated['planning_cycle'];
            $plan->files_json = [
                'annual' => [],
                'Q1'     => [],
                'Q2'     => [],
                'Q3'     => [],
            ];
            $plan->save();
        }

        return redirect()->route('action_planning.create');
    }
    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        $school = Auth::guard('school')->user();

        // 1. Validation
        $validated = $request->validate([
            'planning_cycle' => 'required|in:Ετήσιος,Τριμηνιαίος',
            'slot'           => 'nullable|required_if:planning_cycle,Τριμηνιαίος|in:Q1,Q2,Q3',
            'file'           => 'required|file|mimes:pdf,xlsx,docx,jpeg,png|max:5120', // Max 5MB
        ]);

        // 2. Εύρεση ή δημιουργία της ΜΟΝΑΔΙΚΗΣ εγγραφής του σχολείου
        $plan = ActionPlanning::firstOrNew([
            'school_id' => $school->id,
        ]);

        // 3. Αρχικοποίηση της δομής JSON αν είναι κενή
        $currentFilesJson = $plan->files_json ? $plan->files_json : [
            'annual' => [],
            'Q1'     => [],
            'Q2'     => [],
            'Q3'     => [],
        ];

        // 4. Καθορισμός του στόχου (σε ποιο κλειδί θα μπει το αρχείο)
        $targetKey = ($validated['planning_cycle'] === 'Ετήσιος') ? 'annual' : $validated['slot'];

        // 5. Εύρεση του επόμενου διαθέσιμου counter για το όνομα του αρχείου
        // Σαρώνουμε όλα τα υπάρχοντα αρχεία σε όλες τις κατηγορίες για να βρούμε το μέγιστο counter
        $schoolCode = $school->code; // Βεβαιώσου ότι το model School έχει πεδίο 'code'
        $maxCounter = 0;

        foreach ($currentFilesJson as $category => $filesArray) {
            foreach ($filesArray as $serverName => $originalName) {
                // Έλεγχος αν το όνομα ταιριάζει με το pattern: schoolCode_ΑΡΙΘΜΟΣ.ext
                if (preg_match('/^' . preg_quote($schoolCode, '/') . '_([0-9]+)_/', $serverName, $matches)) {
                    $counter = (int)$matches[1];
                    if ($counter > $maxCounter) {
                        $maxCounter = $counter;
                    }
                }
            }
        }

        $nextCounter = $maxCounter + 1;
        $file = $request->file('file');
        $serverFileName = $schoolCode . "_" . $nextCounter . "_" . $validated['planning_cycle'] . "." . $file->getClientOriginalExtension();
        $directory = "action_planning";

        // 6. Upload μέσω του custom FilesController
        $fileHandler = new FilesController();
        $uploaded = $fileHandler->upload_file($directory, $file, 'local', $serverFileName);

        if ($uploaded->getStatusCode() == 500) {
            Log::channel('files')->error($school->name . " Action Planning file failed to upload");
            return back()->with('failure', 'Αποτυχία στην υποβολή του αρχείου. Δοκιμάστε ξανά.');
        }

        // 7. Ενημέρωση του JSON με το νέο αρχείο στη σωστή κατηγορία
        $currentFilesJson[$targetKey][$serverFileName] = $file->getClientOriginalName();
        
        // 8. Ενημέρωση του Model
        $plan->fill([
            'planning_cycle' => $validated['planning_cycle'],
            'files_json'     => $currentFilesJson,
            'submitted'      => 1,
            'checked'        => 0, // Μηδενίζουμε το checked όταν ανεβαίνει νέο αρχείο (προαιρετικό)
        ]);

        try {
            $plan->save();
        } catch (\Exception $e) {
            Log::channel('files')->error($school->name . " Action Planning failed to update database field files_json");
            return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων. Δοκιμάστε ξανά.');
        }

        // (Προαιρετικά: Αν χρειάζεται να στείλεις και εδώ στο πρωτόκολλο, πρόσθεσε τη λογική εδώ)

        return redirect()->route('action_planning.create') // Βεβαιώσου ότι αυτό είναι το όνομα της route σου
                        ->with('success', 'Η υποβολή του προγραμματισμού ολοκληρώθηκε με επιτυχία.');
    }

    //Διαγραφή αρχείου
    public function delete_file(ActionPlanning $actionPlanning, $serverFileName)
    {
        $school = Auth::guard('school')->user();

        // Ασφάλεια: Βεβαιώσου ότι το plan ανήκει στο συγκεκριμένο σχολείο
        if ($actionPlanning->school_id !== $school->id) {
            abort(403, 'Μη εξουσιοδοτημένη πρόσβαση.');
        }

        $filesJson = $actionPlanning->files_json ?? [
            'annual' => [], 'Q1' => [], 'Q2' => [], 'Q3' => []
        ];
        
        $fileDeleted = false;
        $deletedOriginalName = '';
        $deletedCategory = '';

        // Αναζήτηση σε όλες τις κατηγορίες
        foreach (['annual', 'Q1', 'Q2', 'Q3'] as $key) {
            if (isset($filesJson[$key][$serverFileName])) {
                $deletedOriginalName = $filesJson[$key][$serverFileName];
                $deletedCategory = $key;
                unset($filesJson[$key][$serverFileName]); // Διαγραφή από τον πίνακα
                $fileDeleted = true;
                break;
            }
        }

        if (!$fileDeleted) {
            return back()->with('failure', 'Το αρχείο δεν βρέθηκε στο ιστορικό.');
        }

        $fileHandler = new FilesController();
        try {
            // Διαγραφή από το filesystem
            $fileHandler->delete_file('action_planning', $serverFileName, 'local');
            
            // Αποθήκευση του ενημερωμένου JSON
            $actionPlanning->files_json = $filesJson;
            $actionPlanning->save();
            
        } catch (\Exception $e) {
            Log::channel('files')->error($school->name . " Failed to delete file: " . $serverFileName);
            return back()->with('failure', 'Αποτυχία διαγραφής αρχείου από τον διακομιστή.');
        }

        return back()->with('success', 'Επιτυχής διαγραφή αρχείου: "' . $deletedOriginalName . '"');
    }

    public function download_file($serverFileName, $databaseFileName = null)
    {

        if (auth('school')->check()) {
            $owner = auth('school')->user()->code;
        } elseif (auth('consultant')->check()) {
            $owner = auth('consultant')->user()->surname;
        } else {
            abort(403);
        }
        $directory = "action_planning";
        
        $fileHandler = new FilesController();
        $download = $fileHandler->download_file($directory, $serverFileName, 'local', $databaseFileName);
        
        if ($download->getStatusCode() == 500) {
            Log::channel('files')->error($owner . " File $databaseFileName failed to download");
            return back()->with('failure', 'Αποτυχία λήψης αρχείου. Δοκιμάστε ξανά.');
        }
        
        Log::channel('files')->info($owner . " File $databaseFileName successfully downloaded");
        return $download;
    }

    public function consultant_index()
    {
        $consultant = Auth::guard('consultant')->user();

        $schools = \App\Models\School::whereIn('id', $consultant->schregion->schools->pluck('id'))
        // ->with('actionPlanning')
        ->get();
        return view('microapps.action_planning.index', compact('schools'));
    }

    /**
     * Ο σύμβουλος δηλώνει ότι είδε τον προγραμματισμό του σχολείου.
     * Δεν πρόκειται για έλεγχο/έγκριση, απλώς ένδειξη ότι το είδε ("Έλαβα γνώση").
     * Λειτουργεί ως toggle: αν είναι ήδη μαρκαρισμένο, το κουμπί το αναιρεί.
     */
    public function mark_seen(Request $request, ActionPlanning $actionPlanning)
    {
        $consultant = Auth::guard('consultant')->user();

        // Ασφάλεια: το σχολείο του plan πρέπει να ανήκει στην περιοχή του συμβούλου
        $allowedSchoolIds = $consultant->schregion->schools->pluck('id');
        if (!$allowedSchoolIds->contains($actionPlanning->school_id)) {
            abort(403, 'Μη εξουσιοδοτημένη πρόσβαση.');
        }

        $actionPlanning->checked = $actionPlanning->checked ? 0 : 1;

        try {
            $actionPlanning->save();
        } catch (\Exception $e) {
            Log::channel('files')->error($consultant->surname . " Failed to update 'checked' for ActionPlanning #" . $actionPlanning->id);
            return back()->with('failure', 'Αποτυχία ενημέρωσης. Δοκιμάστε ξανά.');
        }

        return back()->with('success', $actionPlanning->checked
            ? 'Μαρκαρίστηκε ότι είδατε τον προγραμματισμό.'
            : 'Η ένδειξη αφαιρέθηκε.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ActionPlanning $actionPlanning)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ActionPlanning $actionPlanning)
    {
        //
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ActionPlanning $actionPlanning)
    {
        //
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActionPlanning $actionPlanning)
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