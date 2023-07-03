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
    /**
     * Import and read the xlsx file
     *
     * @param Request $request The HTTP request object.
     * @param String $my_app the kind of the app eg fileshare or microapp
     * @param Integer $my_id the id of the microapp or fileshare
     * @return \Illuminate\View\View The rendered view.
     */
    public function importStakeholdersWhoCan(Request $request, $my_app, $my_id){
        
        // validation of input file type
        $rule = [
            'upload_whocan' => 'required|mimes:xlsx'
        ];
        $validator = Validator::make($request->all(), $rule);

        $my_url = "/".$my_app."_profile/".$my_id;
        if($validator->fails()){ 
            return redirect(url($my_url))->with('failure', 'Μη επιτρεπτός τύπος αρχείου!!!');
           
        }

        //store the file
        $filename = "whocan_file_".$my_app.$my_id."_".Auth::id().".xlsx";
        $path = $request->file('upload_whocan')->storeAs('files', $filename);

        //load the file with phpspreadsheet
        $mime = Storage::mimeType($path);
        $spreadsheet = IOFactory::load("../storage/app/$path");
        $whocan_array=array();

        //iterate through file line by line
        $row=1;
        $error=0;
        $rowSumValue="1";
        if($request->all()['template_file']=='schools'){ //if user uploads school codes
            session(['who' => 'schools']);   
            while ($rowSumValue != "" && $row<10000){
                $check=array();
                $check['code'] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();

                //cross check field value with schools table
                if(!School::where('code', $check['code'])->count()){
                    $error=1;
                    $check['code']="Error: Άγνωστος κωδικός σχολείου";
                    $check['id'] = "Error";
                }
                else{
                    $check['id'] = School::where('code', $check['code'])->first()->id;
                }
                //prepare whocan_array to pass it in session  
                array_push($whocan_array, $check);
                
                //change line and check if first column is empty
                $row++;
                $rowSumValue = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();   
            }
            //add testing school manually to always exists in whocans
            array_push($whocan_array, ['code'=>9999999, 'id'=>School::where('code', 9999999)->first()->id]);
        }
        else{ //if user uploads teachers afms
            session(['who' => 'teachers']);   
            while ($rowSumValue != "" && $row<10000){
                $check=array();
                $check['afm'] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();

                //sanitize if it's copied from myschool  ("=9999999")
                if(strlen($check['afm'])>9){
                    $check['afm'] = substr($check['afm'], 2, -1); // remove from start =" and remove from end "
                }

                //cross check field values with database
                if(!Teacher::where('afm', $check['afm'])->count()){
                    $error=1;
                    $check['afm']="Error: Άγνωστος ΑΦΜ εκπαιδευτικού";
                    $check['id'] = "Error";
                }
                else{
                    $check['id'] = Teacher::where('afm',$check['afm'])->first()->id;
                } 
                
                //prepare whocan array to pass it in session for later use
                array_push($whocan_array, $check);
                
                //change line and check if first column is empty
                $row++;
                $rowSumValue = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();      
            } 
            //add testing teacher manually to always exists in whocans
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

    /**
     * Insert the data from session in database
     *
     * @param Request $request The HTTP request object.
     * @param String $my_app the kind of the app eg fileshare or microapp
     * @param Integer $my_id the id of the microapp or fileshare
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
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
                    'stakeholder_type' => $type,
                    'hasAnswer'=> 0
                ]);
            }
        }

        return redirect(url("/".$url."_profile/$my_id"))->with('success', "Η ενημέρωση των $string που μπορούν να $action έγινε επιτυχώς");    
    }

    /**
     * Delete all stakeholders from a microapp or fileshare
     *
     * @param Request $request The HTTP request object.
     * @param String $my_app the kind of the app eg fileshare or microapp
     * @param Integer $my_id the id of the microapp or fileshare
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
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

    /**
     * Delete one stakeholder from a microapp or fileshare
     *
     * @param Request $request The HTTP request object.
     * @param String $my_app the kind of the app eg fileshare or microapp
     * @param Integer $my_id the id of the stakeholder
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function delete_one_whocan(Request $request, $my_app, $my_id){
        if($my_app=='fileshare'){
            FileshareStakeholder::destroy($my_id);
        }
        else if($my_app=='microapp'){
            MicroappStakeholder::destroy($my_id);
        }

        return back()->with('success', 'Ο χρήστης διαγράφηκε');
    }

    /**
     * preview the email will be sent to users, using testing school or teacher
     *
     * @param String $my_app the kind of the app eg fileshare or microapp
     * @param Integer $my_id the id of the microapp or fileshare
     * @return \App\Mail\MicroappToSubmit Returns an instance of the mailable MicroappToSubmit.
     */
    public function preview_mail_to_all($my_app, $my_id){
        if($my_app=="microapp"){
            $stakeholders = Microapp::find($my_id)->stakeholders;
            foreach($stakeholders as $stakeholder){
                if($stakeholder->stakeholder->code==9999999 or $stakeholder->stakeholder->afm==999999999)
                    //returns the view that the mailable uses
                    return new MicroappToSubmit($stakeholder);
            }
        }
        else if($my_app=="fileshare"){
            $stakeholders = Fileshare::find($my_id)->stakeholders;
            foreach($stakeholders as $stakeholder){
                if($stakeholder->stakeholder->code==9999999 or $stakeholder->stakeholder->afm==999999999)
                //returns the view that the mailable uses
                    return new FilesToReceive($stakeholder);
            } 
        }
    }

    /**
     * send one mail to each of the stakeholders
     *
     * @param String $my_app the kind of the app eg fileshare or microapp
     * @param Integer $my_id the id of the stakeholder or fileshare
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
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

    /**
     * send one email to each of the stakeholders of a microapp that have not submitted an answer
     *
     * @param String $my_app the kind of the app eg fileshare or microapp
     * @param Integer $my_id the id of the microapp
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function send_to_all_that_have_not_submitted($my_app, $my_id){
        if($my_app=='microapp'){
            $microapp = Microapp::find($my_id);
            $stakeholders = $microapp->stakeholders;  
            foreach($stakeholders as $stakeholder){
                if(!$stakeholder->hasAnswer){
                    Mail::to($stakeholder->stakeholder->mail)->send(new MicroappToSubmit($stakeholder));
                }  
            }
        }
        
        return back()->with('success', 'Ενημερώθηκαν όσοι ενδιαφερόμενοι δεν έχουν υποβάλλει απάντηση');
    }
}


/**
 * send one email to all the stakeholders of a microapp or fileshare
 * @param Request $request the incoming request
 * @param String $my_app the kind of the app eg fileshare or microapp
 * @param Integer $my_id the id of the microapp
 * @return \Illuminate\Http\RedirectResponse The redirect response.
 */
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