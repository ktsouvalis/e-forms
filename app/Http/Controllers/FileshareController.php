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
use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Validator;

class FileshareController extends Controller
{
    /**
     * Insert a new fileshare into database.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function insert_fileshare(Request $request)//page use
    {
        $table = $this->validate_and_prepare($request);
        $result = $this->create_fileshare($table);

        if($result->getStatusCode() == 500){
            Log::channel('throwable_db')->error(Auth::user()->username." insert_fileshare: ".$e->getMessage());
            return redirect(url('/fileshares'))->with('failure', 'Κάποιο πρόβλημα προέκυψε (throwable_db)');
        }
        else if($result->getStatusCode() == 200){  
            $fileshare = Fileshare::find($result->getData()->fileshare);
            Log::channel('user_memorable_actions')->info(Auth::user()->username." insert_fileshare ".$fileshare->name." for ".$fileshare->department->name);
            return redirect(url('/fileshares'))->with('success', 'Ο διαμοιρασμός αρχείων δημιουργήθηκε. Μπορείτε να προσθέσετε αρχεία, ενδιαφερόμενους στη συνέχεια.'); 
        }
    }

    private function validate_and_prepare(Request $request){ //page use
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

    public function create_fileshare($table){ //app use
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
    public function update_fileshare(Request $request, Fileshare $fileshare) //page use
    {
        $error=false;
        // Update name
        $name = $request->all()['name'];
        $update_name = $this->update_fileshare_name($name, $fileshare);
        if($update_name->getStatusCode() == 500){
            $error=true;
            Log::channel('throwable_db')->error(Auth::user()->username." error update_fileshare: ".$fileshare->id);
        }
        else if($update_name->getStatusCode() == 200){
            Log::channel('user_memorable_actions')->info(Auth::user()->username." update_fileshare (rename) $fileshare->id to ".$fileshare->name);
        }
        // Update common files
        $files1 = $request->file('fileshare_common_files');
        if($files1){     
            $update_common_files = $this->update_fileshare_files($fileshare, $files1, 'common');
            if(isset($update_common_files->getData()->error)){
                Log::channel('files')->error(Auth::user()->username." error update_fileshare (common files): ".$fileshare->id);
                $error=true;
            }
            else{
                Log::channel('files')->info(Auth::user()->username." success update_fileshare (common files): ".$fileshare->id);   
            }
        }
        //update personal files
        $files2 = $request->file('fileshare_personal_files');
        if($files2){  
            $update_personal_files = $this->update_fileshare_files($fileshare, $files2, 'personal');
            if(isset($update_personal_files->getData()->error)){
                Log::channel('files')->error(Auth::user()->username." error update_fileshare (personal files): ".$fileshare->id);
                $error=true;
            }
            else{
                Log::channel('files')->info(Auth::user()->username." success update_fileshare (personal files): ".$fileshare->id);   
            }
        }

        if(!$error)
            return redirect(url("/fileshare_profile/$fileshare->id"))->with('success', 'Ο διαμοιρασμός αρχείων ενημερώθηκε');
        else
            return redirect(url("/fileshare_profile/$fileshare->id"))->with('warning', 'Ο διαμοιρασμός αρχείων ενημερώθηκε με σφάλματα στα αρχεία (throwable_db/files)');   
    }

    public function update_fileshare_name($name, Fileshare $fileshare){ //app use
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
        ], 201);
    }

    public function update_fileshare_files(Fileshare $fileshare, $files, $string){//app_use. string is 'common' or 'personal'
        $error=false;
        $directory = $string == 'common' ? 'fileshare'.$fileshare->id : 'fileshare'.$fileshare->id.'/personal_files';
        // store  files
        foreach ($files as $file){
            $fileHandler = new FilesController();
            $filename = $file->getClientOriginalName();
            $upload  = $fileHandler->upload_file($directory, $file, 'local');
            if($upload->getStatusCode() == 500){
                $error=true;
            }
        }
        if(!$error)
            return response()->json([
                'success' => "Fileshare $string files updated"
            ]);
        else
            return response()->json([
                'error' => "Some Fileshare $string files not updated"
            ]);
    }

    /**
     * Delete a whole fileshare.
     *
     * @param  Request  $request
     * @param  Fileshare  $fileshare
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete_fileshare(Fileshare $fileshare) //page_use
    {
        $username = Auth::check() ? Auth::user()->username : "API";
        $error=false;
        // delete database record
        try{
            Fileshare::destroy($fileshare->id);
        }
        catch(\Exception $e){
            Log::channel('throwable_db')->error($username."failed to delete_fileshare: ".$e->getMessage());
            return back()->with('failure', 'Ο διαμοιρασμός αρχείων δεν διαγράφηκε (throwable_db)');
        }
        
        //delete files from disk
        $directoryHandler = new FilesController();
        $directory = 'fileshare'.$fileshare->id;
        $delete_directory = $directoryHandler->delete_directory($directory, 'local');
        if($delete_directory->getStatusCode() == 500){
            Log::channel('files')->error($username." Fileshare directory $directory failed to delete");
            $error=true;
        }
        else
            Log::channel('files')->info($username." Fileshare directory $directory deleted successfully");
        Log::channel('user_memorable_actions')->info($username." delete_fileshare ".$fileshare->name);
        if(!$error){
            return redirect(url('/fileshares'))->with('success', "Η κοινοποίηση αρχείων $fileshare->name διαγράφηκε");
        }
        else{
            return redirect(url('/fileshares'))->with('warning', "Η κοινοποίηση αρχείων $fileshare->name διαγράφηκε με σφάλματα (files)");
        }
    }

    /**
     * Download a file from the fileshare.
     *
     * @param  Request  $request
     * @param  Fileshare  $fileshare
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download_file(Fileshare $fileshare, $original_filename) //page use
    {
        $username = Auth::check() ? Auth::user()->username : (Auth::guard('school')->check() ? Auth::guard('school')->user()->name : Auth::guard('teacher')->user()->afm);
        $directory = request()->input('personal') == 1 ? 'fileshare'.$fileshare->id.'/personal_files' : 'fileshare'.$fileshare->id;
        $fileHandler = new FilesController();
        $download = $fileHandler->download_file($directory, $original_filename, 'local');
        if($download->getStatusCode() == 500){
            Log::channel('files')->error($username." File $original_filename failed to download");
            return back()->with('failure', 'Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($username." File $original_filename successfully downloaded");
        return $download;
    }

    /**
     * Delete a file from the fileshare.
     *
     * @param  Request  $request
     * @param  Fileshare  $fileshare
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete_file(Fileshare $fileshare, $original_filename){ //page use
        $directory = request()->input('personal') == 1 ? 'fileshare'.$fileshare->id.'/personal_files' : 'fileshare'.$fileshare->id;
        $fileHandler = new FilesController();
        $delete = $fileHandler->delete_file($directory, $original_filename, 'local');
        if($delete->getStatusCode() == 500){
            Log::channel('files')->error(Auth::user()->username." File $original_filename failed to delete");
            return back()->with('failure', 'Το αρχείο δεν διαγράφηκε');
        }
       
        Log::channel('files')->info(Auth::user()->username." File $original_filename deleted successfully");
        return back()->with('success', "Το αρχείο $original_filename αφαιρέθηκε από τον διαμοιρασμό");
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

            // Regular expression for a 7-digit number
            $regex7 = '/(?<!\d)\d{7}(?!\d)/';

            $fieldOfInterest = '';
        
            if (preg_match($regex9, $string, $matches)) {
                $fieldOfInterest = 'afm';//the number is afm
            } 
            else if (preg_match($regex6, $string, $matches)) {
                $fieldOfInterest = 'am';//else the number is am
            }
            else if (preg_match($regex7, $string, $matches)){
                $fieldOfInterest = 'code';//else the number is code
            }
            if(!empty($fieldOfInterest)){
                if($fieldOfInterest == 'am' or $fieldOfInterest == 'afm'){
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
                else{
                    $stakeholder = School::where($fieldOfInterest, $matches[0])->first();
                    if($stakeholder){
                        try{
                            FileshareStakeholder::updateOrCreate(
                            [
                                'fileshare_id' => $fileshare->id,
                                'stakeholder_id' => $stakeholder->id,
                                'stakeholder_type' => 'App\Models\School'
                            ],
                            [
                                'addedby_id' => Auth::user()->id,
                                'addedby_type' => get_class(Auth::user()),
                                'visited_fileshare' => 0
                            ]);
                            $check['stakeholder']=$stakeholder->code.' '.$stakeholder->name;
                        }
                        catch(Throwable $e){
                            Log::channel('throwable_db')->error(Auth::user()->username." auto_update_whocan: ".$e->getMessage());
                            $error=true;
                        }
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

    public function add_comment(Fileshare $fileshare, Request $request){
        $validator = Validator::make($request->all(), [
            'comment' => 'max:5000',
        ]);
        if($validator->fails()){
            return back()->with('warning', 'Το σχόλιό σας ξεπερνάει το όριο των 5000 χαρακτήρων');
        }
        $comment= $request->input('comment');
        $sanitizedComment = strip_tags($comment, '<p><a><b><i><u><ul><ol><li>'); //allow only these tags
        $fileshare->comment = $sanitizedComment;
        $fileshare->save();

        return back()->with('success', 'Το σχόλιο αποθηκεύτηκε');
    }
}
