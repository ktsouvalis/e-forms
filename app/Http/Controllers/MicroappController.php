<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use App\Models\Microapp;
use App\Models\MicroappUser;
use Illuminate\Http\Request;
use App\Models\MicroappStakeholder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class MicroappController extends Controller
{
    //
    public function insertMicroapp(Request $request){
        $incomingFields = $request->all();
        // foreach($incomingFields as $key=>$value){
        //     echo $key.' '. $value.'<br>';
        // }
        // exit;
        try{
            $record = Microapp::create([
                'name' => $incomingFields['microapp_name'],
                'url' => $incomingFields['microapp_url'],
                'color' => $incomingFields['microapp_color'],
                'icon' => $incomingFields['microapp_icon'],
                'active' => 1,
                'visible' => 0,
                'accepts' => 0,
                'opens_at' => $incomingFields['microapp_opens_at'],
                'closes_at' => $incomingFields['microapp_closes_at']
            ]);
        } 
        catch(Throwable $e){
            return redirect(url('/microapps'))
                ->with('failure', "Κάποιο πρόβλημα προέκυψε κατά την εκτέλεση της εντολής, προσπαθήστε ξανά.")
                ->with('old_data', $request->all());
        }

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

        MicroappUser::create([
            'microapp_id'=>$record->id,
            'user_id'=>1,
            'can_edit' => 1
        ]);
        
         MicroappUser::create([
            'microapp_id'=>$record->id,
            'user_id'=>2,
            'can_edit' => 1
        ]); 
        return redirect(url('/microapps'))->with('success', 'Τα στοιχεία της εφαρμογής καταχωρήθηκαν επιτυχώς');
    }

    public function changeMicroappStatus(Request $request){
        if($request->all()['asks_to'] == 'ch_vis_status'){
            $microapp = Microapp::find($request->all()['microapp_id']);
            $microapp->visible = $microapp->visible==1?0:1;
            $microapp->accepts = 0;
            $microapp->save();
        }
        if($request->all()['asks_to'] == 'ch_acc_status'){
            $microapp = Microapp::find($request->all()['microapp_id']);
            $microapp->accepts = $microapp->accepts==1?0:1;
            $microapp->save();
        }
        return redirect(url('/microapps'));
    }

    public function saveProfile(Microapp $microapp, Request $request){

        $incomingFields = $request->all();

        $microapp->name = $incomingFields['name'];
        $microapp->url = $incomingFields['url'];
        $microapp->color = $incomingFields['color'];
        $microapp->icon = $incomingFields['icon'];
        $edited=false;
        // check if changes happened to microapp table
        if($microapp->isDirty()){
            if($microapp->isDirty('name')){
                $given_name = $incomingFields['name'];

                if(Microapp::where('name', $given_name)->count()){
                    return redirect(url("/microapp_profile/$microapp->id"))->with('failure',"Υπάρχει ήδη μικροεφαρμογή με name $given_name.");
                }
            }
            else{
                if($microapp->isDirty('url')){
                    $given_url = $incomingFields['url'];

                    if(Μicroapp::where('url', $given_url)->count()){
                        $existing_microapp =Μicroapp::where('url',$given_url)->first();
                        return redirect(url("/microapp_profile/$microapp->id"))->with('failure',"Υπάρχει ήδη μικροεφαρμογή με url $given_url: $existing_microapp->name");
                    }
                }
            }
            $microapp->save();
            $edited = true;
        }
        
        $old_records = $microapp->users;
        $microapp->users()->delete();

        foreach($request->all() as $key=>$value){
            if(substr($key,0,4)=='user'){ //checks if some user's checkbox is checked
                $user_id=$value;
                if(isset($incomingFields['edit'.$user_id])){ //checks if the radio buttons comes correct and their values
                    $can_edit = $incomingFields['edit'.$user_id]=='no'?0:1;
                }
                else{
                    $can_edit = $old_records->where('user_id', $user_id)->first()->can_edit;
                }
                MicroappUser::create([
                    'microapp_id' => $microapp->id,
                    'user_id' => $user_id,
                    'can_edit' => $can_edit
                ]);
            }
        }

        return redirect(url("/microapp_profile/$microapp->id"))->with('success',"Επιτυχής αποθήκευση των στοιχείων και των χρηστών της μικροεφαρμογής $microapp->name");
    }

    public function importStakeholdersWhoCan(Request $request, Microapp $microapp){

        $rule = [
            'upload_whocan' => 'required|mimes:xlsx'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return redirect(url("/microapp_profile/$microapp->id"))->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
        }

        $filename = "whocan_file".$microapp->id."_".Auth::id().".xlsx";
        $path = $request->file('upload_whocan')->storeAs('files', $filename);
        $mime = Storage::mimeType($path);
        $spreadsheet = IOFactory::load("../storage/app/$path");
        $whocan_array=array();
        $row=1;
        $error=0;
        $rowSumValue="1";
        if($request->all()['template_file']=='schools'){
            session(['who' => 'schools']);   
            while ($rowSumValue != "" && $row<10000){
                $code = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();
                if(!School::where('code', $code)->count()){
                    $error=1;
                    $code="Error: Άγνωστος κωδικός σχολείου";
                }  
                array_push($whocan_array, $code); 
                $row++;
                $rowSumValue = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();   
            }
        }
        else{
            session(['who' => 'teachers']);   
            while ($rowSumValue != "" && $row<10000){
                $afm = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();
                $afm = substr($afm, 2, -1); // remove from start =" and remove from end "
                if(!Teacher::where('afm', $afm)->count()){
                    $error=1;
                    $afm="Error: Άγνωστος ΑΦΜ εκπαιδευτικού";
                }  
                array_push($whocan_array, $afm);
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

    public function insertWhocans(Request $request, Microapp $microapp){
        $microapp->stakeholders()->delete();
        $whocan_array = session('whocan_array');
        $who = session('who');
        if($who=='schools'){
            $type = 'App\Models\School';
        }
        else{
            $type = 'App\Models\Teacher';
        }
        foreach($whocan_array as $code){
            MicroappStakeholder::create([
                'microapp_id' => $microapp->id,
                'stakeholder_id' => $code,
                'stakeholder_type' => $type
            ]);
        }

        return redirect(url("/microapp_profile/$microapp->id"))->with('success', "Η ενημέρωση των σχολείων που μπορούν να υποβάλλουν $microapp->name έγινε επιτυχώς");
    }
}
