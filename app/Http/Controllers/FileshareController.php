<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use App\Models\Fileshare;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\FileshareDepartment;
use Illuminate\Support\Facades\Log;
use App\Models\FileshareStakeholder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class FileshareController extends Controller
{
    /**
     * Insert a new fileshare into database.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function insert_fileshare(Request $request)
    {
        $table = $this->validate_and_prepare($request);
        $result = $this->create_fileshare($table);

        if($result->getStatusCode() == 500){
            Log::channel('throwable_db')->error(Auth::user()->username." insert_fileshare: ".$e->getMessage());
            return redirect(url('/fileshares'))->with('failure', 'Κάποιο πρόβλημα προέκυψε, δείτε το log throwable_db');
        }
        else if($result->getStatusCode() == 200){  
            $fileshare = Fileshare::find($result->getData()->fileshare);
            Log::channel('user_memorable_actions')->info(Auth::user()->username." insert_fileshare ".$fileshare->name." for ".$fileshare->department->name);
            return redirect(url('/fileshares'))->with('success', 'Ο διαμοιρασμός αρχείων δημιουργήθηκε. Μπορείτε να προσθέσετε αρχεία, ενδιαφερόμενους στη συνέχεια.'); 
        }
    }

    private function validate_and_prepare(Request $request){
        if($request->user()->can('chooseDepartment', Fileshare::class)){
            $department_id = $request->input('department');
        }
        else{
            $department_id = $request->user()->department->id;
        }
        $table = array();
        $table['name'] = $request->all()['fileshare_name'];
        $table['department_id'] = $department_id;

        return $table;
    }

    public function create_fileshare($table){
        try{
            $fileshare = Fileshare::create($table);
        }
        catch(Throwable $e){
            return response()->json([
                'error' => 'Fileshare creation failed'
            ], 500);
        }
        return response()->json([
            'success' => 'Fileshare created successfully',
            'fileshare' => $fileshare->id
        ], 200);
    }

    /**
     * Update an existing fileshare.
     *
     * @param  Request  $request
     * @param  Fileshare  $fileshare
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_fileshare(Request $request, Fileshare $fileshare)
    {
        $error=false;
        $name = $request->all()['name'];
        $update_name = $this->update_fileshare_name($name, $fileshare);
        if($update_name->getStatusCode() == 500){
            $error=true;
            Log::channel('throwable_db')->error(Auth::user()->username." error update_fileshare: ".$fileshare->id);
        }
        else if($update_name->getStatusCode() == 200){
            Log::channel('user_memorable_actions')->info(Auth::user()->username." update_fileshare (rename) $fileshare->id to ".$fileshare->name);
        }

        $files1 = $request->file('fileshare_common_files');
        if($files1){     
            $update_common_files = $this->update_fileshare_files($fileshare, $files1, 'common');
            if($update_common_files->getStatusCode() == 500){
                $error=true;
                Log::channel('files')->error(Auth::user()->username." error update fileshare common files: ".$fileshare->id);
            }
            else if($update_common_files->getStatusCode() == 200){
                Log::channel('files')->info(Auth::user()->username." update fileshare common files) $fileshare->id");
            }
        }
        
        $files2 = $request->file('fileshare_personal_files');
        if($files2){  
            $update_personal_files = $this->update_fileshare_files($fileshare, $files2, 'personal');
            if($update_personal_files->getStatusCode() == 500){
                $error=true;
                Log::channel('files')->error(Auth::user()->username." error update fileshare personal files: ".$fileshare->id);
            }
            else if($update_personal_files->getStatusCode() == 200){
                Log::channel('files')->info(Auth::user()->username." update fileshare personal files) $fileshare->id");
            }
        }
        if(!$error)
            return redirect(url("/fileshare_profile/$fileshare->id"))->with('success', 'Ο διαμοιρασμός αρχείων ενημερώθηκε');
        else
            return redirect(url("/fileshare_profile/$fileshare->id"))->with('warning', 'Ο διαμοιρασμός αρχείων ενημερώθηκε με σφάλματα στα αρχεία throwable_db/files της ημέρας');   
    }

    public function update_fileshare_name($name, Fileshare $fileshare){
        $old_name = $fileshare->name;
        // Update name
        $fileshare->name = $name;
        if($fileshare->isDirty('name')){
            try{
                $fileshare->save();
            }
            catch(\Exception $e){
                return response()->json([
                    'error' => 'Fileshare name not updated'
                ], 500);
            }
            return response()->json([
                'success' => 'Fileshare name updated'
            ], 200);
        }
        return response()->json([
            'success' => 'Fileshare not changed'
        ], 200);
    }

    public function update_fileshare_files(Fileshare $fileshare, $files, $string){//string is 'common' or 'personal'
        if($string == 'common'){
            $directory = 'fileshare'.$fileshare->id;
              
        }
        else if($string == 'personal'){
            $directory = 'fileshare'.$fileshare->id.'/personal_files';
            
        }
        // store  files
        foreach ($files as $file){
            try {
                $path = $file->storeAs($directory, $file->getClientOriginalName(), 'local');
            } 
            catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        return response()->json([
            'success' => "Fileshare $string files updated"
        ], 200);
    }

    /**
     * Delete a whole fileshare.
     *
     * @param  Request  $request
     * @param  Fileshare  $fileshare
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete_fileshare(Request $request, Fileshare $fileshare)
    {
        //delete files from disk
        Storage::disk('local')->deleteDirectory('fileshare'.$fileshare->id);

        Log::channel('user_memorable_actions')->info(Auth::user()->username." delete_fileshare ".$fileshare->name);
        
        // delete database record
        Fileshare::destroy($fileshare->id);

        return redirect(url('/fileshares'))->with('success', "Η κοινοποίηση αρχείων $fileshare->name διαγράφηκε");
    }

    /**
     * Download a file from the fileshare.
     *
     * @param  Request  $request
     * @param  Fileshare  $fileshare
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download_file(Request $request, Fileshare $fileshare)
    {
        //get filename from hidden input from the UI
        $file = $request->input('filename');
        
        if(Auth::guard('school')->check())Log::channel('stakeholders_fileshares')->info(Auth::guard('school')->user()->code." download_file $file ".$fileshare->name);
        if(Auth::guard('teacher')->check())Log::channel('stakeholders_fileshares')->info(Auth::guard('teacher')->user()->afm." download_file $file ".$fileshare->name);
        $response = Storage::disk('local')->download($file);  
        ob_end_clean();
        return $response;
    }

    /**
     * Delete a file from the fileshare.
     *
     * @param  Request  $request
     * @param  Fileshare  $fileshare
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete_file(Request $request, Fileshare $fileshare)
    {
        //get filename from hidden input from the UI
        $file = $request->input('filename');
        
        Storage::disk('local')->delete($file);
        $fn = basename($file);

        Log::channel('user_memorable_actions')->info(Auth::user()->username." delete_file $fn from ".$fileshare->name);
        return back()->with('success', "Το αρχείο $fn αφαιρέθηκε από τον διαμοιρασμό");
    }

    public function auto_update_whocan(Fileshare $fileshare, Request $request){
        $directory_personal = '/fileshare'.$fileshare->id.'/personal_files';
        $files_personal = Storage::disk('local')->files($directory_personal);
        $stakeholders_array=array();
        $error=false;  
        foreach($files_personal as $file_p){
            $check=array();
            $string = basename($file_p); 
            $check['filename']=$string;
            // Regular expression for a 6-digit number
            $regex6 = '/(?<!\d)\d{6}(?!\d)/';

            // Regular expression for a 9-digit number
            $regex9 = '/(?<!\d)\d{9}(?!\d)/';
            $fieldOfInterest = '';
        
            if (preg_match($regex9, $string, $matches)) {
                $fieldOfInterest = 'afm';//the number is afm
            } 
            else if (preg_match($regex6, $string, $matches)) {
                $fieldOfInterest = 'am';//else the number is am
            }
            if(!empty($fieldOfInterest)){
                $stakeholder = Teacher::where($fieldOfInterest, $matches[0])->first();
                if($stakeholder){
                    try{
                        FileshareStakeholder::updateOrCreate(
                        [
                            'fileshare_id' => $fileshare->id,
                            'stakeholder_id' => $stakeholder->id,
                            'stakeholder_type' => 'App\Models\Teacher'
                        ],
                        [
                            'addedby_id' => Auth::user()->id,
                            'addedby_type' => get_class(Auth::user()),
                            'visited_fileshare' => 0
                        ]);
                        $check['stakeholder']=$stakeholder->surname.' '.$stakeholder->name;
                    }
                    catch(Throwable $e){
                        Log::channel('throwable_db')->error(Auth::user()->username." auto_update_whocan: ".$e->getMessage());
                        $error=true;
                    }
                }
            }
            array_push($stakeholders_array,$check);
        }
        session()->flash('stakeholders_array', $stakeholders_array);
        if($error)
            return back()->with('warning', 'Κάποιες εισαγωγές απέτυχαν, δείτε το log thorwable_db');
        else
            return back()->with('success', 'Οι ενδιαφερόμενοι προστέθηκαν αυτόματα με βάση τους ΑΜ/ΑΦΜ που βρέθηκαν στα αρχεία');
    }

    public function school_informs_teachers(Fileshare $fileshare, Request $request){
        $school = Auth::guard('school')->user();
        $teachers = $school->organikis->merge($school->ypiretisis);
        $stakeholders_array=array();
        $i=0;
        $error=false;
        foreach($teachers as $teacher){
            $check=array();
            try{
                FileshareStakeholder::updateOrCreate(
                [
                    'fileshare_id' => $fileshare->id,
                    'stakeholder_id' => $teacher->id,
                    'stakeholder_type' => 'App\Models\Teacher'
                ],
                [
                    'addedby_id' => $school->id,
                    'addedby_type' => get_class($school),
                    'visited_fileshare' => 0
                ]);
                $i++;
                $check['stakeholder']=$i.'. '.$teacher->surname.' '.$teacher->name;
            }
            catch(Throwable $e){
                Log::channel('throwable_db')->error(Auth::guard('school')->user()->name." school_informs_teachers: ".$e->getMessage());
                $error=true;
            }
            array_push($stakeholders_array,$check);
        }
        $count = $teachers->count();
        session()->flash('stakeholders_array', $stakeholders_array);
        $message = "Eνημερώθηκαν $i/$count εκπαιδευτικοί";
        if($error){
            return back()->with('warning', $message);
        }
        else{
            return back()->with('success', $message);
        }
    }

    public function allow_schools(Fileshare $fileshare, Request $request){
        if($request->input('checked')=='true')
            $fileshare->allow_school = 1;
        else
            $fileshare->allow_school = 0;
        $fileshare->save();

        return response()->json(['message' => 'Fileshare updated successfully']);
    }
}
