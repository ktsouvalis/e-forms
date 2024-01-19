<?php

namespace App\Http\Controllers;

use App\Models\Filecollect;
use App\Models\MicroappUser;
use Illuminate\Http\Request;
use App\Models\FilecollectUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FilecollectController extends Controller
{
    //
    public function insert_filecollect(Request $request)
    {
        $filecollect_table = $this->validate_and_prepare($request);
        $result = $this->create_filecollect($filecollect_table);
        if($result->getStatusCode() == 200){
            $file_upload_result = $this->upload_file($request->file('filecollect_original_file'), $result->original['filecollect']);
            $filecollect_users_table = $this->prepare_users_table($request, $result->original['filecollect']);
            $result_insert_users = $this->insert_filecollect_users($filecollect_users_table);
            if($result_insert_users->getStatusCode() == 500){
                Log::channel('throwable_db')->error(Auth::user()->username." insert_filecollect_users: ".$e->getMessage());
                return redirect(url('/filecollects'))->with('failure', 'Η Συλλογή αρχείων δημιουργήθηκε. Κάποιο πρόβλημα προέκυψε με την εισαγωγή χρηστών.');
            }
            else if($result->getStatusCode() == 200){  
                $filecollect = Filecollect::find($result->getData()->filecollect);
                Log::channel('user_memorable_actions')->info(Auth::user()->username." insert_filecollect ".$filecollect->name);
                return redirect(url('/filecollects'))->with('success', 'Η συλλογή αρχείων δημιουργήθηκε με επιτυχία. Μπορείτε να προσθέσετε ενδιαφερόμενους στη συνέχεια. Μην ξεχάσετε να "ανοίξετε" την υποβολή!'); 
            }
        }elseif($result->getStatusCode() == 500){
            Log::channel('throwable_db')->error(Auth::user()->username." insert_filecollect: ".$e->getMessage());
            return redirect(url('/filecollects'))->with('failure', 'Κάποιο πρόβλημα προέκυψε (throwable_db). Η Συλλογή Αρχείων δε δημιουργήθηκε.');
        }
    }

    public function upload_file($file, $filecollect_id){//app_use
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

    private function validate_and_prepare(Request $request){ //page use
        $table = array();
        $table['name'] = $request->all()['filecollect_name'];
        $table['user_id'] = Auth::user()->id;
        $table['fileMime'] = $request->all()['filecollect_mime'];
        $table['visible'] = 0;
        $table['accepts'] = 0;
        return $table;
    }

    public function create_filecollect($table){ //app use
        try{
            $filecollect = Filecollect::create($table);
        }
        catch(Throwable $e){
            return response()->json([
                'error' => 'Fileshare creation failed'
            ], 500);
        }
        return response()->json([
            'success' => 'Fileshare created successfully',
            'filecollect' => $filecollect->id
        ], 200);
    }

   //check if some other users except the admins are coming in from the input and add these records to the filecollects_users table
   private function prepare_users_table(Request $request, $filecollect_id){
        $table = array();
        foreach($request->all() as $key=>$value){
            if(substr($key,0,4)=='user'){
                $user_id=$value;
                
                if(isset($request->all()['edit'.$user_id])){
                    $can_edit = $request->all()['edit'.$user_id]=='no'?0:1;
                }
                array_push($table, [
                    'filecollect_id'=>$filecollect_id,
                    'user_id'=>$user_id,
                    'can_edit'=> $can_edit,
                ]);
                
            }
        }
        return $table;
    }

    public function insert_filecollect_users($users_table){
        foreach($users_table as $table){
            try{
                FilecollectUser::create($table);
            }
            catch(Throwable $e){
                Log::channel('throwable_db')->error(Auth::user()->username." insertFilecollect (add users) ".' '.$e->getMessage());
                return response()->json([
                    'error' => 'Filecollect users insertion failed'
                ], 500);
            }
        }

        return response()->json([
            'success' => 'Filecollect users inserted successfully'
        ], 200);

    }

/**
     * Save changes of the microapp profile
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Microapp $filecollect
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveProfile(Filecollect $filecollect, Request $request){

        $incomingFields = $request->all();

        $filecollect->name = $incomingFields['name'];
        $filecollect->user_id = Auth::user()->id;
        $filecollect->fileMime = $incomingFields['filecollect_mime'];
        $edited=false;
        // if there is a file to upload -> update file
        if($request->file('filecollect_original_file'))
            $file_upload_result = $this->upload_file($request->file('filecollect_original_file'), $filecollect->id);
        // check if changes happened to microapp table
        if($filecollect->isDirty()){
            // if name has changed
            if($filecollect->isDirty('name')){
                $given_name = $incomingFields['name'];

                // if there is already a microapp with the newly given name
                if(Filecollect::where('name', $given_name)->count()){
                    return redirect(url("/filecollect_profile/$filecollect->id"))->with('failure',"Υπάρχει ήδη συλλογή αρχείων με όνομα $given_name.");
                } 
            }
            
            $filecollect->save();
            $edited = true;
        }
        if($request->user()->can('addUser', Filecollect::class)){
            // everything is going to be deleted from the filecollect_users table and rewriten
            $old_records = $filecollect->users;
            $filecollect->users()->delete();

            //check if some other users except the admins are coming in from the input and add these records to the microapps_users table
            foreach($request->all() as $key=>$value){
                if(substr($key,0,4)=='user'){ //checks if some user's checkbox is checked
                    $user_id=$value;
                    if(isset($incomingFields['edit'.$user_id])){ //checks if the radio buttons and their values come as excpected 
                        $can_edit = $incomingFields['edit'.$user_id]=='no'?0:1; //for a new user in filecollect checks radiobuttons
                    }
                    else{
                        $can_edit = $old_records->where('user_id', $user_id)->first()->can_edit; // for an existing user with no changes in radios
                    }
                    FilecollectUser::create([
                        'filecollect_id' => $filecollect->id,
                        'user_id' => $user_id,
                        'can_edit' => $can_edit
                    ]);
                }
            }
        }
        return redirect(url("/filecollect_profile/$filecollect->id"))->with('success',"Επιτυχής αποθήκευση των στοιχείων και των χρηστών της Συλλογής Αρχείων $filecollect->name");
    }

}
