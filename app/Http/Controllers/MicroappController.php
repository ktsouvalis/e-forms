<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use App\Models\Microapp;
use App\Models\Superadmin;
use App\Models\MicroappUser;
use Illuminate\Http\Request;
use App\Models\MicroappStakeholder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MicroappController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('boss' )->only(['update']);
        
    }

    public function index(){
        return view('manage.microapps.index');
    }

    public function edit($id){
        $microapp = Microapp::findOrFail($id);
        if($microapp->active){
            if(Auth::user()->can('update', $microapp))
                return view('manage.microapps.edit', ['microapp' => $microapp]);
            else
                abort(403);
        }
        else{
            abort(404);
        }
    }
    /**
     * Create a record for the new microapp, add the two admins in the microapps_users table, add other users regarding if the can edit the microapp or not.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request){
        $incomingFields = $request->all();
        
        try{
            $record = Microapp::create([
                'name' => $incomingFields['microapp_name'],
                'url' => $incomingFields['microapp_url'],
                'color' => $incomingFields['microapp_color'],
                'icon' => $incomingFields['microapp_icon'],
                'active' => 1,
                'visible' => 0,
                'accepts' => 0,
                // 'opens_at' => $incomingFields['microapp_opens_at'],
                'closes_at' => $incomingFields['microapp_closes_at']
            ]);
            Log::channel('user_memorable_actions')->info(Auth::user()->username." insertMicroapp ".$record->name);
        } 
        catch(Throwable $e){
            Log::channel('throwable_db')->error(Auth::user()->username." insertMicroapp (create) ". $e->getMessage());
            return back()
                ->with('failure', "Κάποιο πρόβλημα προέκυψε κατά την εκτέλεση της εντολής, δείτε το log throwable_db.")
                ->with('old_data', $request->all());
        }

        //check if some other users except the admins are coming in from the input and add these records to the microapps_users table
        foreach($request->all() as $key=>$value){
            if(substr($key,0,4)=='user'){
                $user_id=$value;
                if(isset($incomingFields['edit'.$user_id])){
                    $can_edit = $incomingFields['edit'.$user_id]=='no'?0:1;
                }
                try{
                    MicroappUser::create([
                        'microapp_id'=>$record->id,
                        'user_id'=>$user_id,
                        'can_edit'=> $can_edit
                    ]);
                }
                catch(Throwable $e){
                    Log::channel('throwable_db')->error(Auth::user()->username." insertMicroapp (add users) ".' '.$e->getMessage());
                    return back()->with('warning', 'Η μικροεφαρμογή δημιουργήθηκε αλλά οι χρήστες δεν προστέθηκαν. Δείτε το log throwable_db');
                }
            }
        }

        return back()->with('success', 'Τα στοιχεία της εφαρμογής καταχωρήθηκαν επιτυχώς');
    }

    /**
     * Activate or deactivate a microapp.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Microapp $microapp
     * @return \Illuminate\Http\RedirectResponse
     */
    public function onOff(Request $request, Microapp $microapp){
        $this->authorize('deactivate', $microapp);
        if($microapp->active){ 
            $microapp->active=0; //deactivate the microapp
            $microapp->visible=0; //change visibility value
            $microapp->accepts=0; // change acceptability value
            $microapp->stakeholders()->delete(); // delete all stakeholders
            $microapp->users()->where('user_id', '<>', 1)->where('user_id', '<>', 2)->delete(); //delete all_users except tsouvalis and stefanopoulos
            $text="Η μικροεφαρμογή απενεργοποιήθηκε";
            Log::channel('user_memorable_actions')->info(Auth::user()->username."microapp onOff (deactivate) ".$microapp->name);
        }
        else{
            $microapp->active=1; //activate the microapp
            $text="Η μικροεφαρμογή ενεργοποιήθηκε. Πρέπει να προσθέσετε χρήστες και ενδιαφερόμενους (σχολεία/εκπαιδευτικούς)";
            Log::channel('user_memorable_actions')->info(Auth::user()->username."microapp onOff (activate) ".$microapp->name);
        }
        $microapp->save();

        return back()->with('success', $text);
    }

    /**
     * Change visibility or acceptability of a microapp
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Microapp $microapp
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeMicroappStatus(Request $request, Microapp $microapp){
        $this->authorize('update', $microapp);
        if($request->all()['asks_to'] == 'ch_vis_status'){
            $microapp->visible = $microapp->visible==1?0:1; //change visibility based on previous state
            $microapp->accepts = 0; // reset acceptability
            $microapp->save();
            Log::channel('user_memorable_actions')->info(Auth::user()->username." changeMicroappStatus (change visibility) ".$microapp->name);
        }
        if($request->all()['asks_to'] == 'ch_acc_status'){
            $microapp->accepts = $microapp->accepts==1?0:1; // change acceptability based on previous state
            $microapp->save();
            Log::channel('user_memorable_actions')->info(Auth::user()->username." changeMicroappStatus (change acceptability) ".$microapp->name);
        }
        return back()->with('success', 'H κατάσταση της εφαρμογής άλλαξε επιτυχώς');
    }

    /**
     * Save changes of the microapp profile
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Microapp $microapp
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Microapp $microapp, Request $request){
        $incomingFields = $request->all();

        $microapp->name = $incomingFields['name'];
        $microapp->url = $incomingFields['url'];
        $microapp->color = $incomingFields['color'];
        $microapp->icon = $incomingFields['icon'];
        $edited=false;

        // check if changes happened to microapp table
        if($microapp->isDirty()){
            // if name has changed
            if($microapp->isDirty('name')){
                $given_name = $incomingFields['name'];

                // if there is already a microapp with the newly given name
                if(Microapp::where('name', $given_name)->count()){
                    return back()->with('failure',"Υπάρχει ήδη μικροεφαρμογή με name $given_name.");
                } 
            }
            if($microapp->isDirty('url')){
                // if url has changed
                $given_url = $incomingFields['url'];

                // if there is already a microapp with the newly given url
                if(Microapp::where('url', $given_url)->count()){
                    $existing_microapp =Μicroapp::where('url',$given_url)->first();
                    return back()->with('failure',"Υπάρχει ήδη μικροεφαρμογή με url $given_url: $existing_microapp->name");
                }
            }
            
            $microapp->save();
            $edited = true;
        }
        if($request->user()->can('addUser', Microapp::class)){
            // everything is going to be deleted from the microapps_users table and rewriten
            $old_records = $microapp->users;
            $microapp->users()->delete();

            //check if some other users except the admins are coming in from the input and add these records to the microapps_users table
            foreach($request->all() as $key=>$value){
                if(substr($key,0,4)=='user'){ //checks if some user's checkbox is checked
                    $user_id=$value;
                    if(isset($incomingFields['edit'.$user_id])){ //checks if the radio buttons and their values come as excpected 
                        $can_edit = $incomingFields['edit'.$user_id]=='no'?0:1; //for a new user in microapp checks radiobuttons
                    }
                    else{
                        $can_edit = $old_records->where('user_id', $user_id)->first()->can_edit; // for an existing user with no changes in radios
                    }
                    MicroappUser::create([
                        'microapp_id' => $microapp->id,
                        'user_id' => $user_id,
                        'can_edit' => $can_edit
                    ]);
                }
            }
        }
        return back()->with('success',"Επιτυχής αποθήκευση των στοιχείων και των χρηστών της μικροεφαρμογής $microapp->name");
    }
}
