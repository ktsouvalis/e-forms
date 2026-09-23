<?php

namespace App\Http\Controllers\microapps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\microapps\ActionPlanning;
use Illuminate\Support\Facades\Auth;
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
        //
        return view('microapps.action_planning.create', ['appname' => 'action_planning']);
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        $school = Auth::guard('school')->user();

        // 1. Validation
        $validated = $request->validate([
            'planning_cycle' => ['required', 'in:Ετήσιος,Τριμηνιαίος'],
            'slot'           => ['nullable', 'required_if:planning_cycle,Τριμηνιαίος', 'in:Q1,Q2,Q3'],
            'file'           => ['required', 'file', 'mimes:pdf,xlsx,docx,jpeg,png', 'max:10240'],
        ]);

        // 2. Καθορισμός του κλειδιού μέσα στο JSON
        $slotKey = $validated['planning_cycle'] === 'Ετήσιος'
            ? 'annual'
            : $validated['slot']; // Q1, Q2 ή Q3

        // 3. Upload αρχείου
        $file     = $request->file('file');
        $filename = time() . '_' . $school->id . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $path     = $file->storeAs('action_planning/' . $school->id, $filename, 'public');

        $newFileEntry = [
            'path'        => $path,
            'filename'    => $file->getClientOriginalName(),
            'uploaded_at' => now()->toDateTimeString(),
        ];

        // 4. Εύρεση ή δημιουργία της ΜΟΝΑΔΙΚΗΣ εγγραφής του σχολείου
        $plan = ActionPlanning::firstOrCreate(
            ['school_id' => $school->id],
            [
                'planning_cycle' => $validated['planning_cycle'],
                'files_json'     => [
                    'annual' => [],
                    'Q1'     => [],
                    'Q2'     => [],
                    'Q3'     => [],
                ],
                'submitted' => 0,
                'checked'   => false,
            ]
        );

        // 5. Ανάγνωση υπάρχοντος JSON (fallback σε άδεια δομή)
        $history = $plan->files_json ?? [
            'annual' => [],
            'Q1'     => [],
            'Q2'     => [],
            'Q3'     => [],
        ];

        // 6. Προσθήκη του νέου αρχείου στο σωστό κλειδί
        $history[$slotKey][] = $newFileEntry;

        // 7. Update
        $plan->update([
            'planning_cycle' => $validated['planning_cycle'],
            'files_json'     => $history,
            'submitted'      => 1,
            'checked'        => false,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Ο προγραμματισμός υποβλήθηκε επιτυχώς!');
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
