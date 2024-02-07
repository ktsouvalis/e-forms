<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Filecollect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\FilecollectStakeholder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FilecollectController extends Controller
{
    //
    public function insert_filecollect(Request $request)
    {
        $filecollect_table = $this->validate_and_prepare($request);
        $result = $this->create_filecollect($filecollect_table);
        if($result->getStatusCode() == 200){
            $filecollect = Filecollect::find($result->getData()->filecollect);
            Log::channel('user_memorable_actions')->info(Auth::user()->username." insert_filecollect ".$filecollect->name);
            return redirect(url("/filecollect_profile/$filecollect->id"))->with('success', 'Η συλλογή αρχείων δημιουργήθηκε με επιτυχία. Μπορείτε να προσθέσετε ενδιαφερόμενους στη συνέχεια. Μην ξεχάσετε να "ανοίξετε" την υποβολή!'); 
        }
        else{
            Log::channel('throwable_db')->error(Auth::user()->username." insert_filecollect: ".$e->getMessage());
            return redirect(url('/filecollects'))->with('failure', 'Κάποιο πρόβλημα προέκυψε (throwable_db). Η Συλλογή Αρχείων δε δημιουργήθηκε.');
        }
    }

    public function update_file(Request $request, Filecollect $filecollect, $type){
        if($type=='base'){
            $file = $request->file('base_file');
            $msg = "Η εγκύκλιος";
        }
        else{
            $file = $request->file('template_file');
            $msg = "Το πρότυπο";
        }
        $upload_result = $this->upload_file($file, $filecollect->id);
        if(isset($upload_result->getData()->success)){
            if($type=='base')
                $filecollect->base_file = $file->getClientOriginalName();
            else
                $filecollect->template_file = $file->getClientOriginalName();
            $filecollect->save();
        }
        else{
            Log::channel('files')->error(Auth::user()->username." filecollect $filecollect->id update $type: ".$e->getMessage());
            return back()->with('failure', 'Κάποιο πρόβλημα προέκυψε (files). Ενημερώστε τον διαχειριστή του συστήματος.');
        }
        Log::channel('files')->info(Auth::user()->username." filecollect $filecollect->id update $type success");
        return back()->with('success', $msg.' ενημερώθηκε');
    }

    private function upload_file($file, $filecollect_id){//app_use
        $error=false;
        $directory = 'file_collects/'.$filecollect_id;
        // store  file
        $fileHandler = new FilesController();
        $upload  = $fileHandler->upload_file($directory, $file, 'local');
        if($upload->getStatusCode() == 500){
            $error=true;
        }
        if(!$error)
            return response()->json([
                'success' => "File uploaded"
            ]);
        else
            return response()->json([
                'error' => "Files not uploaded"
            ]);
    }

    public function update_comment(Request $request, Filecollect $filecollect){
        $validator = Validator::make($request->all(), [
            'comment' => 'max:5000',
        ]);
        if($validator->fails()){
            return back()->with('warning', 'Το σχόλιό σας ξεπερνάει το όριο των 5000 χαρακτήρων');
        }
        $comment= $request->input('comment');
        $sanitizedComment = strip_tags($comment, '<p><a><b><i><u><ul><ol><li>'); //allow only these tags
        $filecollect->comment = $sanitizedComment;
        $filecollect->save();
        Log::channel('user_memorable_actions')->info(Auth::user()." updated filecollect $filecollect->id comment");
        return back()->with('success', 'Το σχόλιο αποθηκεύτηκε');    
    }

    private function validate_and_prepare(Request $request){ //page use
        if($request->user()->can('chooseDepartment', Filecollect::class)){
            $department_id = $request->input('department');
        }
        else{
            $department_id = $request->user()->department->id;
        }
        $table = array();
        $table['name'] = $request->all()['filecollect_name'];
        $table['department_id'] = $department_id;
        $table['fileMime'] = $request->all()['filecollect_mime'];
        $table['visible'] = 0;
        $table['accepts'] = 0;
        return $table;
    }

    private function create_filecollect($table){ //app use
        try{
            $filecollect = Filecollect::create($table);
        }
        catch(Throwable $e){
            return response()->json([
                'error' => 'Filecollect creation failed'
            ], 500);
        }
        return response()->json([
            'success' => 'Filecollect created successfully',
            'filecollect' => $filecollect->id
        ], 200);
    }

    public function saveProfile(Filecollect $filecollect, Request $request){

        $incomingFields = $request->all();

        $filecollect->name = $incomingFields['name'];
        $filecollect->fileMime = $incomingFields['filecollect_mime'];
        $edited=false;
            
        // check if changes happened to filecollect table
        if($filecollect->isDirty()){
            // if name has changed
            if($filecollect->isDirty('name')){
                $given_name = $incomingFields['name'];

                // if there is already a filecollect with the newly given name
                if(Filecollect::where('name', $given_name)->count()){
                    return redirect(url("/filecollect_profile/$filecollect->id"))->with('failure',"Υπάρχει ήδη συλλογή αρχείων με όνομα $given_name.");
                } 
            }
            $filecollect->save();
            $edited = true;
        }
        Log::channel('user_memorable_actions')->info(Auth::user()." updated filecollect $filecollect->id basic info");
        return redirect(url("/filecollect_profile/$filecollect->id"))->with('success',"Επιτυχής αποθήκευση των στοιχείων της Συλλογής $filecollect->name");
    }

    public function changeFilecollectStatus(Request $request, Filecollect $filecollect){
        if($request->all()['asks_to'] == 'ch_vis_status'){
            $filecollect->visible = $filecollect->visible==1?0:1; //change visibility based on previous state
            $filecollect->accepts = 0; // reset acceptability
            $filecollect->save();
            Log::channel('user_memorable_actions')->info(Auth::user()->username." changeFilecollectStatus (change visibility) ".$filecollect->name);
        }
        if($request->all()['asks_to'] == 'ch_acc_status'){
            $filecollect->accepts = $filecollect->accepts==1?0:1; // change acceptability based on previous state
            $filecollect->save();
            Log::channel('user_memorable_actions')->info(Auth::user()->username." changeFilecollectStatus (change acceptability) ".$filecollect->name);
        }
        return back()->with('success', 'H κατάσταση της εφαρμογής άλλαξε επιτυχώς');
    }

    public function post_filecollect(Request $request, Filecollect $filecollect){
        $record_to_update=null;
        //identify stakeholder
        if(Auth::guard('school')->check()){
            $record_to_update = Auth::guard('school')->user()->filecollects->where('filecollect_id', $filecollect->id)->first();
            $identifier = Auth::guard('school')->user()->code;
        }
        else if(Auth::guard('teacher')->check()){
            $record_to_update = Auth::guard('teacher')->user()->filecollects->where('filecollect_id', $filecollect->id)->first(); 
            $identifier = Auth::guard('school')->user()->afm;   
        }
    
        if(!$record_to_update){
            abort(403);
        }
        else{
            //prepare extension for file based on the fileMime
            if($request->file('the_file')){
                $extension="";
                if($filecollect->fileMime == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
                    $extension ='.xlsx';
                }
                else if($filecollect->fileMime == "application/pdf"){
                    $extension = ".pdf";
                }
                else if($filecollect->fileMime == "application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
                    $extension = ".doc";
                }

                //validate the input file
                $rule = [
                    'the_file' => "mimetypes:$filecollect->fileMime"
                ];
                $validator = Validator::make($request->all(), $rule);
                if($validator->fails()){ 
                    return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
                }

                //$file is for the file field of the database
                $file = $request->file('the_file')->getClientOriginalName();

                //$filename is the name with which the file will be saved. save the file
                $filename = $identifier.'_filecollect_'.$filecollect->id.$extension;
                try{
                    $path = $request->file('the_file')->storeAs("file_collects/$filecollect->id", $filename);
                }
                catch(\Exception $e){
                    Log::channel('files')->error($identifier.' failure to upload file for filecollect '. $filecollect->id.' '.$e.getMessage());
                    return back()->with('failure', 'Η ενέργεια απέτυχε (files). Επικοινωνήστε με τον διαχειριστή του συστήματος');
                }
                Log::channel('files')->info($identifier.' success to upload file for filecollect '. $filecollect->id);

                //prepare the record to update and save it
                $record_to_update->file = $file;
                    if($request->input('stake_comment'))
                        $record_to_update->stake_comment = $request->input('stake_comment');
                try{
                    $record_to_update->save();
                }
                catch(Exception $e){
                    Log::channel('throwable_db')->error($identifier.' failure to update database for filecollect '. $filecollect->id.' '.$e.getMessage()); 
                    return back()->with('failure', 'Η ενέργεια απέτυχε (throwable_db). Επικοινωνήστε με τον διαχειριστή του συστήματος');  
                }
                return back()->with('success', 'Η ενέργεια ολοκληρώθηκε!');  
            }
        }
    }

    public function getSchoolFile(Request $request, FilecollectStakeholder $old_data){
        $extension="";
        if($old_data->filecollect->fileMime == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
            $extension ='.xlsx';
        }
        else if($old_data->filecollect->fileMime == "application/pdf"){
            $extension = ".pdf";
        }
        else if($old_data->filecollect->fileMime == "application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
            $extension = ".doc";
        }

        if(get_class($old_data->stakeholder) == 'App\Models\School')
            $identifier = $old_data->stakeholder->code;
        else if(get_class($old_data->stakeholder) == 'App\Models\Teacher')
            $identifier = $old_data->stakeholder->afm;
        
        $filename = $identifier.'_filecollect_'.$old_data->filecollect->id.$extension;
        $file = "file_collects/".$old_data->filecollect->id."/".$filename;
        if(Storage::disk('local')->exists($file)){
            $response = Storage::disk('local')->download($file, $old_data->file);  
            ob_end_clean();
            try{
                return $response;
            }
            catch(\Exception $e){
                return back()->with('failure', 'Δεν ήταν δυνατή η λήψη του αρχείου, προσπαθήστε ξανά');    
            }
        } 
        else 
            return back()->with('failure', 'Το αρχείο δεν υπάρχει.');
        }
}
