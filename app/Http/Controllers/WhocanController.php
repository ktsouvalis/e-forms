<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use App\Models\Microapp;
use App\Models\Fileshare;
use App\Mail\FilesToReceive;
use Illuminate\Http\Request;
use App\Mail\MicroappToSubmit;
use App\Models\MicroappStakeholder;
use App\Models\FileshareStakeholder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class WhocanController extends Controller
{
    //
    public function importStakeholdersWhoCan(Request $request, $my_app, $my_id){
        
        $rule = [
            'upload_whocan' => 'required|mimes:xlsx'
        ];
        $validator = Validator::make($request->all(), $rule);

        $my_url = "/".$my_app."_profile/".$my_id;
        if($validator->fails()){ 
            return redirect(url($my_url))->with('failure', 'Μη επιτρεπτός τύπος αρχείου!!!');
           
        }

        $filename = "whocan_file_".$my_app.$my_id."_".Auth::id().".xlsx";
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
                $check=array();
                $check['code'] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();
                if(!School::where('code', $check['code'])->count()){
                    $error=1;
                    $check['code']="Error: Άγνωστος κωδικός σχολείου";
                    $check['id'] = "Error";
                }
                else{
                    $check['id'] = School::where('code', $check['code'])->first()->id;
                }  
                array_push($whocan_array, $check);
                
                $row++;
                $rowSumValue = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();   
            }
            array_push($whocan_array, ['code'=>9999999, 'id'=>School::where('code', 9999999)->first()->id]);
        }
        else{
            session(['who' => 'teachers']);   
            while ($rowSumValue != "" && $row<10000){
                $check=array();
                $check['afm'] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();
                if(strlen($check['afm'])>9){
                    $check['afm'] = substr($check['afm'], 2, -1); // remove from start =" and remove from end "
                }
                if(!Teacher::where('afm', $check['afm'])->count()){
                    $error=1;
                    $check['afm']="Error: Άγνωστος ΑΦΜ εκπαιδευτικού";
                    $check['id'] = "Error";
                }
                else{
                    $check['id'] = Teacher::where('afm',$check['afm'])->first()->id;
                }  
                array_push($whocan_array, $check);
                
                $row++;
                $rowSumValue = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();      
            } 
            array_push($whocan_array, ['afm'=>999999999, 'id'=>Teacher::where('afm', 999999999)->first()->id]); 
        }
        
        if($error){
            return view('import-whocan', ['asks_to'=>'error', 'my_app' => $my_app, 'my_id' => $my_id, 'whocan_array' => $whocan_array, 'who' => session('who')]);
        }
        else{
            session(['whocan_array' => $whocan_array]);
            return view('import-whocan', ['asks_to'=>'save', 'my_app' => $my_app, 'my_id' => $my_id, 'whocan_array' => $whocan_array, 'who' => session('who')]);
        }
    }

    public function insertWhocans(Request $request, $my_app, $my_id){
        $whocan_array = session('whocan_array');
        $who = session('who');
        if($who=='schools'){
            $type = 'App\Models\School';
            $string="σχολείων";
        }
        else{
            $type = 'App\Models\Teacher';
            $string="εκπαιδευτικών";
        }
        
        if($my_app=="fileshare"){ //fileshare
            $url = "fileshare";
            $action="δουν τα αρχεία";
            foreach($whocan_array as $one_stakeholder){
                FileshareStakeholder::updateOrCreate(
                    [
                    'fileshare_id' => $my_id,
                    'stakeholder_id' => $one_stakeholder['id'],
                    'stakeholder_type' => $type
                    ],
                    [
                    'fileshare_id' => $my_id,
                    'stakeholder_id' => $one_stakeholder['id'],
                    'stakeholder_type' => $type
                ]);
            }
        }
        else if($my_app=="microapp"){ //microapp
            $url="microapp";
            $action="υποβάλλουν";
            foreach($whocan_array as $one_stakeholder){
                MicroappStakeholder::updateOrCreate(
                    [
                    'microapp_id' => $my_id,
                    'stakeholder_id' => $one_stakeholder['id'],
                    'stakeholder_type' => $type
                    ],
                    [
                    'microapp_id' => $my_id,
                    'stakeholder_id' => $one_stakeholder['id'],
                    'stakeholder_type' => $type
                ]);
            }
        }

        return redirect(url("/".$url."_profile/$my_id"))->with('success', "Η ενημέρωση των $string που μπορούν να $action έγινε επιτυχώς");    
    }

    public function delete_all_whocans(Request $request, $my_app, $my_id){
        if($my_app=='fileshare'){
            $fileshare= Fileshare::find($my_id);
            $fileshare->stakeholders()->delete();
        }
        else if($my_app=='microapp'){
            $microapp = Microapp::find($my_id);
            $microapp->stakeholders()->delete();
        }

        return back()->with('success', 'Επιτυχής διαγραφή');
    }

    public function delete_one_whocan(Request $request, $my_app, $my_id){
        if($my_app=='fileshare'){
            FileshareStakeholder::destroy($my_id);
        }
        else if($my_app=='microapp'){
            MicroappStakeholder::destroy($my_id);
        }

        return back()->with('success', 'Ο χρήστης διαγράφηκε');
    }

    public function preview_mail_to_all($my_app, $my_id){
        if($my_app=="microapp"){
            $stakeholders = Microapp::find($my_id)->stakeholders;
            foreach($stakeholders as $stakeholder){
                if($stakeholder->stakeholder->code==9999999 or $stakeholder->stakeholder->afm==999999999)
                    return new MicroappToSubmit($stakeholder);
            }
        }
        else if($my_app=="fileshare"){
            $stakeholders = Fileshare::find($my_id)->stakeholders;
            foreach($stakeholders as $stakeholder){
                if($stakeholder->stakeholder->code==9999999 or $stakeholder->stakeholder->afm==999999999)
                    return new FilesToReceive($stakeholder);
            } 
        }
    }

    public function send_to_all(Request $request, $my_app, $my_id){
        $recipients=array();
        if($my_app=='fileshare'){
            $fileshare = Fileshare::find($my_id);
            $stakeholders = $fileshare->stakeholders;
            foreach($stakeholders as $stakeholder){
                Mail::to($stakeholder->stakeholder->mail)->send(new FilesToReceive($fileshare, $stakeholder));  
            }
        }
        else if($my_app=='microapp'){
            $microapp = Microapp::find($my_id);
            $stakeholders = $microapp->stakeholders;  
            foreach($stakeholders as $stakeholder){
                Mail::to($stakeholder->stakeholder->mail)->send(new MicroappToSubmit($stakeholder));  
            }
        }
        
        return back()->with('success', 'Ενημερώθηκαν όλοι οι ενδιαφερόμενοι');
    }
}

// public function send_to_all(Request $request, $my_app, $my_id){
//         if($my_app=='fileshare'){
//             $fileshare = Fileshare::find($my_id);
//             $emails = Fileshare::where('id', $fileshare->id)
//                 ->with('stakeholders.stakeholder')
//                 ->get()
//                 ->flatMap(function ($fileshare) {
//                     return $fileshare->stakeholders->map(function ($stakeholder) {
//                         return $stakeholder->stakeholder->mail;
//                     });
//                 }); 
//             Mail::bcc($emails)->send(new NewFilesToReceive($fileshare));
//         }
//         else if($my_app=='microapp'){
//             $microapp = Microapp::find($my_id);
//             $emails = Microapp::where('id', $microapp->id)
//                 ->with('stakeholders.stakeholder')
//                 ->get()
//                 ->flatMap(function ($microapp) {
//                     return $microapp->stakeholders->map(function ($stakeholder) {
//                         return $stakeholder->stakeholder->mail;
//                     });
//                 }); 
//             Mail::bcc($emails)->send(new NewMicroappToSubmit($microapp));
//         }
        
        
//         return back()->with('success', 'Ενημερώθηκαν όλοι οι ενδιαφερόμενοι');
//     }