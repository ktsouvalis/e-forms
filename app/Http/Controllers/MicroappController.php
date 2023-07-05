<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use App\Models\Microapp;
use App\Models\Superadmin;
use App\Models\MicroappUser;
use Illuminate\Http\Request;
use App\Models\MicroappStakeholder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class MicroappController extends Controller
{
    
    /**
     * Create a record for the new microapp, add the two admins in the microapps_users table, add other users regarding if the can edit the microapp or not.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function insertMicroapp(Request $request){
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
        } 
        catch(Throwable $e){
            return redirect(url('/microapps'))
                ->with('failure', "Κάποιο πρόβλημα προέκυψε κατά την εκτέλεση της εντολής, προσπαθήστε ξανά.")
                ->with('old_data', $request->all());
        }

        //check if some other users except the admins are coming in from the input and add these records to the microapps_users table
        foreach($request->all() as $key=>$value){
            if(substr($key,0,4)=='user'){
                $user_id=$value;
                if(isset($incomingFields['edit'.$user_id])){
                    $can_edit = $incomingFields['edit'.$user_id]=='no'?0:1;
                }
                MicroappUser::create([
                    'microapp_id'=>$record->id,
                    'user_id'=>$user_id,
                    'can_edit'=> $can_edit
                ]);
            }
        }

        // add superadmins to the microapps_users table with edit privileges
        foreach(Superadmin::all() as $superadmin){
            MicroappUser::create([
                'microapp_id'=>$record->id,
                'user_id'=>$superadmin->user_id,
                'can_edit' => 1
            ]);
        }

        return redirect(url('/microapps'))->with('success', 'Τα στοιχεία της εφαρμογής καταχωρήθηκαν επιτυχώς');
    }

    /**
     * Activate or deactivate a microapp.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Microapp $microapp
     * @return \Illuminate\Http\RedirectResponse
     */
    public function onOff(Request $request, Microapp $microapp){
        if($microapp->active){ 
            $microapp->active=0; //deactivate the microapp
            $microapp->visible=0; //change visibility value
            $microapp->accepts=0; // change acceptability value
            $microapp->stakeholders()->delete(); // delete all stakeholders
            $microapp->users()->where('user_id', '<>', 1)->where('user_id', '<>', 2)->delete(); //delete all_users except tsouvalis and stefanopoulos
            $text="Η μικροεφαρμογή απενεργοποιήθηκε";
        }
        else{
            $microapp->active=1; //activate the microapp
            $text="Η μικροεφαρμογή ενεργοποιήθηκε. Πρέπει να προσθέσετε χρήστες και ενδιαφερόμενους (σχολεία/εκπαιδευτικούς)";
        }
        $microapp->save();

        return redirect(url('/microapps'))->with('success', $text);
    }

    /**
     * Change visibility or acceptability of a microapp
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Microapp $microapp
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeMicroappStatus(Request $request, Microapp $microapp){
        if($request->all()['asks_to'] == 'ch_vis_status'){
            $microapp->visible = $microapp->visible==1?0:1; //change visibility based on previous state
            $microapp->accepts = 0; // reset acceptability
            $microapp->save();
        }
        if($request->all()['asks_to'] == 'ch_acc_status'){
            $microapp->accepts = $microapp->accepts==1?0:1; // change acceptability based on previous state
            $microapp->save();
        }
        return redirect(url('/microapps'))->with('success', 'H κατάσταση της εφαρμογής άλλαξε επιτυχώς');
    }

    /**
     * Save changes of the microapp profile
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Microapp $microapp
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveProfile(Microapp $microapp, Request $request){

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
                    return redirect(url("/microapp_profile/$microapp->id"))->with('failure',"Υπάρχει ήδη μικροεφαρμογή με name $given_name.");
                }
            }
            else{
                // if url has changed
                if($microapp->isDirty('url')){
                    $given_url = $incomingFields['url'];

                    // if there is already a microapp with the newly given url
                    if(Microapp::where('url', $given_url)->count()){
                        $existing_microapp =Μicroapp::where('url',$given_url)->first();
                        return redirect(url("/microapp_profile/$microapp->id"))->with('failure',"Υπάρχει ήδη μικροεφαρμογή με url $given_url: $existing_microapp->name");
                    }
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

            // add admins again in microapp 
            foreach(Superadmin::all() as $superadmin){
                MicroappUser::create([
                    'microapp_id'=>$microapp->id,
                    'user_id'=>$superadmin->user_id,
                    'can_edit' => 1
                ]);
            }
        }
        return redirect(url("/microapp_profile/$microapp->id"))->with('success',"Επιτυχής αποθήκευση των στοιχείων και των χρηστών της μικροεφαρμογής $microapp->name");
    }


    /**
     * Import and check uploaded file for stakeholders
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Microapp $microapp
     * @return \Illuminate\View\View 
     */
    public function importStakeholdersWhoCan(Request $request, Microapp $microapp){
        //validate the file type
        $rule = [
            'upload_whocan' => 'required|mimes:xlsx'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return redirect(url("/microapp_profile/$microapp->id"))->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
        }

        //store the file
        $filename = "whocan_file_m".$microapp->id."_".Auth::id().".xlsx";
        $path = $request->file('upload_whocan')->storeAs('files', $filename);
        $mime = Storage::mimeType($path);

        //load the file to read the contents with phpspreadhsheet
        $spreadsheet = IOFactory::load("../storage/app/$path");
        $whocan_array=array();
        $row=1;
        $error=0;
        $rowSumValue="1";
        if($request->all()['template_file']=='schools'){ //if user is uploading schools
            session(['who' => 'schools']);   
            while ($rowSumValue != "" && $row<10000){
                $check=array();
                $check['code'] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();
                // check if there is not a record in the schools table
                if(!School::where('code', $check['code'])->count()){
                    $error=1;
                    $check['code']="Error: Άγνωστος κωδικός σχολείου";
                    $check['id'] = "Error";
                }
                else{
                    // if there is, get the school's id
                    $check['id'] = School::where('code', $check['code'])->first()->id;
                }
                
                // prepare whocan_array
                array_push($whocan_array, $check);
                
                // check if next line's first cell is empty (to stop the iteration) 
                $row++;
                $rowSumValue = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();   
            }
        }
        else{ //if user is uploading teachers
            session(['who' => 'teachers']);   
            while ($rowSumValue != "" && $row<10000){
                $check=array();
                $check['afm'] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();
                // myschool report has the afms in the format "=999999999"
                if(strlen($check['afm'])>9){
                    $check['afm'] = substr($check['afm'], 2, -1); // remove from start =" and remove from end "
                }
                // check if there is not a record in the teachers table
                if(!Teacher::where('afm', $check['afm'])->count()){
                    $error=1;
                    $check['afm']="Error: Άγνωστος ΑΦΜ εκπαιδευτικού";
                    $check['id'] = "Error";
                }
                else{// if there is, get the teacher's id
                    $check['id'] = Teacher::where('afm',$check['afm'])->first()->id;
                }  

                //prepare whocan array
                array_push($whocan_array, $check);
                
                // check if next line's first cell is empty (to stop the iteration) 
                $row++;
                $rowSumValue = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();      
            }  
        }
        
        if($error){
            return view('import-whocan', ['asks_to'=>'error', 'microapp'=>$microapp, 'whocan_array' => $whocan_array, 'who' => session('who')]);
        }
        else{
            session(['whocan_array' => $whocan_array]);
            return view('import-whocan', ['asks_to'=>'save', 'microapp'=>$microapp, 'whocan_array' => $whocan_array, 'who' => session('who')]);
        }
    }

    /**
     * Insert the stakeholders from the whocan array which is prepared by the importStakeholdersWhoCan() method
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Microapp $microapp
     * @return \Illuminate\Http\RedirectResponse
     */
    public function insertWhocans(Request $request, Microapp $microapp){

        // read whocan array 
        $whocan_array = session('whocan_array');
        
        // prepare for schools or teachers
        $who = session('who');
        if($who=='schools'){
            $type = 'App\Models\School';
            $string="σχολείων";
        }
        else{
            $type = 'App\Models\Teacher';
            $string="εκπαιδευτικών";
        }

        //insert stakeholders in database one by one
        foreach($whocan_array as $one_stakeholder){
            MicroappStakeholder::updateOrCreate(
                [
                'microapp_id' => $microapp->id,
                'stakeholder_id' => $one_stakeholder['id'],
                'stakeholder_type' => $type
                ],
                [
                'microapp_id' => $microapp->id,
                'stakeholder_id' => $one_stakeholder['id'],
                'stakeholder_type' => $type
            ]);
        }

        return redirect(url("/microapp_profile/$microapp->id"))->with('success', "Η ενημέρωση των $string που μπορούν να υποβάλλουν $microapp->name έγινε επιτυχώς");
    }
}
