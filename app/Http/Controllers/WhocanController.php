<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Month;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Microapp;
use App\Models\Fileshare;
use App\Mail\FilesToUpload;
use App\Models\Filecollect;
use App\Mail\FilesToReceive;
use Illuminate\Http\Request;
use App\Mail\MicroappToSubmit;
use App\Models\AccessCriteria;
use App\Models\MicroappStakeholder;
use Illuminate\Support\Facades\Log;
use App\Models\FileshareStakeholder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\FilecollectStakeholder;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class WhocanController extends Controller
{
    /**
     * Import stakeholders to a microapp or fileshare
     *
     * @param Request $request The HTTP request object.
     * @param String $my_app the kind of the app eg fileshare or microapp
     * @param Integer $my_id the id of the microapp or fileshare
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function import_whocans(Request $request, $my_app, $my_id){
        // reading input and seperate identifiers
        $identifiers = explode(',', $request->input('afmscodes'));
        $found=0;
        $not_found=[];
        // Regular expression for a 6-digit number
        $regex6 = '/(?<!\d)\d{6}(?!\d)/';
        // Regular expression for a 9-digit number
        $regex9 = '/(?<!\d)\d{9}(?!\d)/';
        // Regular expression for a 7-digit number
        $regex7 = '/(?<!\d)\d{7}(?!\d)/';

        //iterating through identifiers
        foreach($identifiers as $identifier){
            $fieldOfInterest = '';
            if (preg_match($regex9, $identifier, $matches)) {
                $stakeholder = Teacher::where('afm', $matches[0])->first();//the number is teacher afm
            } 
            else if (preg_match($regex6, $identifier, $matches)) {
                $stakeholder = Teacher::where('am', $matches[0])->first();//the number is teacher am
            }
            else if (preg_match($regex7, $identifier, $matches)) {
                $stakeholder = School::where('code', $matches[0])->first();//the number is school code
            }
            
            if($stakeholder){
                $found=1;
            }
            else{
                array_push($not_found, trim($identifier));
                continue;
            }

            if($my_app=="fileshare"){ //fileshare
                FileshareStakeholder::updateOrCreate(
                    [
                    'fileshare_id' => $my_id,
                    'stakeholder_id' => $stakeholder->id,
                    'stakeholder_type' => get_class($stakeholder)
                    ],
                    [
                    'addedby_id' => Auth::user()->id,
                    'addedby_type' => get_class(Auth::user()),
                    'visited_fileshare'=>0 
                    ]
                ); 
            }
            else if($my_app=="microapp"){ //microapp
                MicroappStakeholder::updateOrCreate(
                    [
                    'microapp_id' => $my_id,
                    'stakeholder_id' => $stakeholder->id,
                    'stakeholder_type' => get_class($stakeholder)
                    ],
                    [
                    'hasAnswer'=> 0
                    ]
                ); 
            }
            else if($my_app=="filecollect"){
                if(!FilecollectStakeholder::where('filecollect_id', $my_id)->where('stakeholder_id' , $stakeholder->id)->where('stakeholder_type' , get_class($stakeholder))->count())
                    FilecollectStakeholder::create([
                        'filecollect_id' => $my_id,
                        'stakeholder_id' => $stakeholder->id,
                        'stakeholder_type' => get_class($stakeholder)
                    ]);
            }      
        }

        Session::put('not_found', $not_found);
        
        if($found){
            Log::channel('user_memorable_actions')->info(Auth::user()->username." imported whocans $my_app $my_id");
            return back()
                ->with('success', "Η ενημέρωση των ενδιαφερόμενων έγινε επιτυχώς");
        }
        else{
            return back()
                ->with('warning', "Δεν βρέθηκε σχολείο ή εκπαιδευτικός για να προστεθεί στους ενδιαφερόμενους");   
        }
    }

    public function import_whocans_with_criteria(Request $request, $my_app, $my_id){
        // dd($request->input('kladoi'));
        if($my_app == 'microapp')$class="App\Models\Microapp";
        else if($my_app == 'fileshare')$class="App\Models\Fileshare";
        else if($my_app == 'filecollect')$class="App\Models\Filecollect";

        $json = array();
        $klados = array();
        $sxesi_ergasias_id = array();
        $org_eae = array();

        $found_kladoi = false;
        foreach($request->input('kladoi') as $kl){
            array_push($klados, $kl); 
            $found_kladoi = true;  
        }

        if(!$found_kladoi){
            return back()->with('warning', 'Δεν επιλέχθηκε κλάδος');
        }

        $found_sxesi_ergasias = false;
        foreach($request->input('sxeseis') as $sx){
            array_push($sxesi_ergasias_id, $sx);  
            $found_sxesi_ergasias = true; 
        }
        if(!$found_sxesi_ergasias){
            return back()->with('warning', 'Δεν επιλέχθηκε σχέση εργασίας');
        }

        $found_eae = false;
        foreach($request->input('org_eae') as $eae){
            array_push($org_eae, $eae);  
            $found_eae = true; 
        }
        if(!$found_eae){
            return back()->with('warning', 'Δεν επιλέχθηκε θέση στην Γενική ή Ειδική Αγωγή');
        }

        $json['klados']= $klados;
        $json['sxesi_ergasias_id']= $sxesi_ergasias_id;
        $json['org_eae']= $org_eae;

        AccessCriteria::updateOrCreate(
            [
            'app_id' => $my_id,
            'app_type' => $class
            ],
            [
            'criteria' => json_encode($json)
            ]
        );
        if($my_app=='microapp' and $request->input('inform_whocan_table')){
            $microapp = Microapp::find($my_id);
            $criteria = json_decode($microapp->accessCriteria->criteria, true);
            $count=0;
            foreach(Teacher::all() as $teacher){
                if(!$teacher->active)continue;
                $satisfiesCriteria = true;
                foreach ($criteria as $key => $value) {
                    if (!in_array($teacher->$key, $value)) {
                        $satisfiesCriteria = false;
                        break;
                    }  
                }
                if($satisfiesCriteria){
                    MicroappStakeholder::updateOrCreate(
                        [
                        'microapp_id' => $my_id,
                        'stakeholder_id' => $teacher->id,
                        'stakeholder_type' => get_class($teacher)
                        ],
                        [
                        'hasAnswer'=> 0
                        ]
                    ); 
                        $count++;
                }  
            }
            if($count)
                return back()->with('success', 'Επιτυχής εισαγωγή κριτηρίων. Προστέθηκαν '.$count.' ενδιαφερόμενοι');
            else 
                return back()->with('warning', 'Δεν βρέθηκαν ενδιαφερόμενοι που να ικανοποιούν τα κριτήρια');
        }
        if($my_app=='filecollect'){
            $count=0;
            $filecollect = Filecollect::find($my_id);
            $criteria = json_decode($filecollect->accessCriteria->criteria, true);
            foreach(Teacher::all() as $teacher){
                if(!$teacher->active)continue;
                $satisfiesCriteria = true;
                foreach ($criteria as $key => $value) {
                    if (!in_array($teacher->$key, $value)) {
                        $satisfiesCriteria = false;
                        break;
                    }  
                }
                if($satisfiesCriteria){
                    if(!FilecollectStakeholder::where('filecollect_id', $my_id)->where('stakeholder_id' , $teacher->id)->where('stakeholder_type' , get_class($teacher))->count())
                        FilecollectStakeholder::create([
                            'filecollect_id' => $my_id,
                            'stakeholder_id' => $teacher->id,
                            'stakeholder_type' => get_class($teacher)
                        ]);
                        $count++;
                }  
            }
            if($count)
                return back()->with('success', 'Επιτυχής εισαγωγή κριτηρίων. Προστέθηκαν '.$count.' ενδιαφερόμενοι');
            else 
                return back()->with('warning', 'Δεν βρέθηκαν ενδιαφερόμενοι που να ικανοποιούν τα κριτήρια');
        }
        if($my_app=='fileshare'){
            $count=0;
            $fileshare = Fileshare::find($my_id);
            $criteria = json_decode($fileshare->accessCriteria->criteria, true);
            foreach(Teacher::all() as $teacher){
                if(!$teacher->active)continue;
                $satisfiesCriteria = true;
                foreach ($criteria as $key => $value) {
                    if (!in_array($teacher->$key, $value)) {
                        $satisfiesCriteria = false;
                        break;
                    }  
                }
                if($satisfiesCriteria){
                    FileshareStakeholder::updateOrCreate(
                    [
                    'fileshare_id' => $my_id,
                    'stakeholder_id' => $teacher->id,
                    'stakeholder_type' => get_class($teacher)
                    ],
                    [
                    'addedby_id' => Auth::user()->id,
                    'addedby_type' => get_class(Auth::user()),
                    'visited_fileshare'=>0 
                    ]
                    ); 
                    $count++;
                }  
            }
            if($count)
                return back()->with('success', 'Επιτυχής εισαγωγή κριτηρίων. Προστέθηκαν '.$count.' ενδιαφερόμενοι');
            else 
                return back()->with('warning', 'Δεν βρέθηκαν ενδιαφερόμενοι που να ικανοποιούν τα κριτήρια');
        }

        return back()->with('success', 'Επιτυχής εισαγωγή κριτηρίων');
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
        else if($my_app=='filecollect'){
            $filecollect = Filecollect::find($my_id);
            $filecollect->stakeholders()->delete();
        }
        Log::channel('user_memorable_actions')->info(Auth::user()->username." deleted all whocans $my_app $my_id");
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
        else if($my_app=='filecollect'){
            FilecollectStakeholder::destroy($my_id);
        }

        Log::channel('user_memorable_actions')->info(Auth::user()->username." deleted one $my_id whocan from stakeholders table of $my_app");
        return back()->with('success', 'Ο χρήστης διαγράφηκε');
    }

    public function delete_whocan_criteria(Request $request, $my_app, $my_id){
        if($my_app == 'microapp')$class="App\Models\Microapp";
        else if($my_app == 'fileshare')$class="App\Models\Fileshare";
        else if($my_app == 'filecollect')$class="App\Models\Filecollect";

        AccessCriteria::where('app_id', $my_id)->where('app_type', $class)->delete();

        return back()->with('success', 'Επιτυχής διαγραφή κριτηρίων');
    }

    public function count_criteria_teachers(AccessCriteria $access_criteria){
        $criteria = json_decode($access_criteria->criteria, true);
        $count=0;
        foreach(Teacher::all() as $teacher){
            if(!$teacher->active)continue;
            $satisfiesCriteria = true;
            foreach ($criteria as $key => $value) {
                if (!in_array($teacher->$key, $value)) {
                    $satisfiesCriteria = false;
                    break;
                }  
            }
            if($satisfiesCriteria){
                $count++;
            }  
        }
        return response(['count'=>$count]);
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
            $stakeholder = Microapp::find($my_id)->stakeholders->first();
            return new MicroappToSubmit($stakeholder, Auth::user()->username);
        }
        else if($my_app=="fileshare"){
            $fileshare = Fileshare::find($my_id);
            $stakeholder = $fileshare->stakeholders->first();
            return new FilesToReceive($fileshare, $stakeholder, Auth::user()->username);
        }
        else if($my_app=="filecollect"){
            $filecollect = Filecollect::find($my_id);
            $stakeholder = $filecollect->stakeholders->first();  
            return new FilesToUpload($filecollect, $stakeholder, Auth::user()->username);
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
        if($my_app=='fileshare'){
            $fileshare = Fileshare::find($my_id);
            $stakeholders = $fileshare->stakeholders;
            foreach($stakeholders as $stakeholder){
                $mail = $stakeholder->stakeholder->mail;
                Mail::to($mail)->send(new FilesToReceive($fileshare, $stakeholder, Auth::user()->username));
            }
            Log::channel('mails')->info(Auth::user()->username. " Fileshare $fileshare->id, MailToStakeholders try");
        }
        else if($my_app=='microapp'){
            $microapp = Microapp::find($my_id);
            $stakeholders = $microapp->stakeholders;  
            foreach($stakeholders as $stakeholder){
                $mail = $stakeholder->stakeholder->mail;
                Mail::to($mail)->send(new MicroappToSubmit($stakeholder, Auth::user()->username));
            }
            Log::channel('mails')->info(Auth::user()->username." Microapp $microapp->name, MailToStakeholders try");
        }
        else if($my_app=='filecollect'){
            $filecollect = Filecollect::find($my_id);
            $stakeholders = $filecollect->stakeholders;
            foreach($stakeholders as $stakeholder){
                $mail = $stakeholder->stakeholder->mail;
                Mail::to($mail)->send(new FilesToUpload($filecollect, $stakeholder, Auth::user()->username));
            }  
            Log::channel('mails')->info(Auth::user()->username." Filecollect $filecollect->id, MailToStakeholders try");
        }
        
        return back()->with('success', 'Θα ξεκινήσει η αποστολή των μηνυμάτων. Μπορείτε να παρακολουθήσετε την πρόοδο τους στο log mails');
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
                $mail = $stakeholder->stakeholder->mail;
                if($microapp->url == "/all_day_school"){
                    // επειδή το ολοήμερο είναι μηνιαία υποβολή, ενημερώνεται το σχολείο αν δεν έχει υποβάλλει τον τρέχοντα μήνα
                    if(!$stakeholder->stakeholder->all_day_schools->where('month_id', Month::getActiveMonth()->id)->count()){
                        Mail::to($mail)->send(new MicroappToSubmit($stakeholder, Auth::user()->username));
                    }
                }
                else{
                    if(!$stakeholder->hasAnswer){
                        Mail::to($mail)->send(new MicroappToSubmit($stakeholder, Auth::user()->username));
                    }  
                }
            }
            $user = Auth::user();
            Log::channel('mails')->info("$my_app $my_id $user->username, MailToThoseWhoOwe try");
        }
        return back()->with('success', 'Θα ξεκινήσει η αποστολή των μηνυμάτων. Μπορείτε να παρακολουθήσετε την πρόοδο τους στο log mails');   
    }

    public function mail_to_those_who_visited_fileshare(Fileshare $fileshare, Request $request){
        $stakeholders = $fileshare->stakeholders->where('visited_fileshare',1);
        if($stakeholders->count())
            foreach($stakeholders as $stakeholder){
                $mail = $stakeholder->stakeholder->mail;
                Mail::to($mail)->send(new FilesToReceive($fileshare, $stakeholder, Auth::user()->username));
            }
        else{
            return back()->with('warning','Δεν υπάρχουν αποδέκτες');
        }
        Log::channel('mails')->info(Auth::user()->username." Fileshare $fileshare->id, Mail to those who visited try");

        return back()->with('success', 'Θα ξεκινήσει η αποστολή των μηνυμάτων. Μπορείτε να παρακολουθήσετε την πρόοδο τους στο log mails');  
    }

    public function mail_to_those_who_not_visited_fileshare(Fileshare $fileshare, Request $request){
        $stakeholders = $fileshare->stakeholders->where('visited_fileshare',0);
        $mail_error = false;
        if($stakeholders->count())
            foreach($stakeholders as $stakeholder){
                $mail = $stakeholder->stakeholder->mail;
                Mail::to($mail)->send(new FilesToReceive($fileshare, $stakeholder, Auth::user()->username));
            }
        else{
            return back()->with('warning','Δεν υπάρχουν αποδέκτες');
        }
        Log::channel('mails')->info(Auth::user()->username." Fileshare $fileshare->id, Mail to those who not visited try");

        return back()->with('success', 'Θα ξεκινήσει η αποστολή των μηνυμάτων. Μπορείτε να παρακολουθήσετε την πρόοδο τους στο log mails');
    }

    public function personal_fileshare_mail(Fileshare $fileshare, Request $request, FileshareStakeholder $stakeholder){
        $mail = $stakeholder->stakeholder->mail;
        Mail::to($mail)->send(new FilesToReceive($fileshare, $stakeholder, Auth::user()->username));
        Log::channel('mails')->info(Auth::user()->username." Fileshare $fileshare->id, personal MailToStakeholder try: $mail"); 
       
        return back()->with('success', 'Θα ενημερωθεί ο ενδιαφερόμενος. Μπορείτε να παρακολουθήσετε την πρόοδο της αποστολής στο log mails');
    }

    public function mail_to_those_who_uploaded_filecollect(Filecollect $filecollect, Request $request){
        $stakeholders = $filecollect->stakeholders->whereNotNull('file');
        if($stakeholders->count())
            foreach($stakeholders as $stakeholder){
                $mail = $stakeholder->stakeholder->mail;
                Mail::to($mail)->send(new FilesToUpload($filecollect, $stakeholder, Auth::user()->username));  
            }
        else{
            return back()->with('warning','Δεν υπάρχουν αποδέκτες');
        }
        Log::channel('mails')->info(Auth::user()->username." Filecollect $filecollect->id, MailToStakeholders try"); 

        return back()->with('success', 'Θα ξεκινήσει η αποστολή των μηνυμάτων. Μπορείτε να παρακολουθήσετε την πρόοδο τους στο log mails');
    }

    public function mail_to_those_who_not_uploaded_filecollect(Filecollect $filecollect, Request $request){
        $stakeholders = $filecollect->stakeholders->where('file',null);
        if($stakeholders->count())
            foreach($stakeholders as $stakeholder){
                $mail = $stakeholder->stakeholder->mail;
                Mail::to($mail)->send(new FilesToUpload($filecollect, $stakeholder, Auth::user()->username)); 
            }
        else{
            return back()->with('warning','Δεν υπάρχουν αποδέκτες');
        }
        Log::channel('mails')->info(Auth::user()->username." Filecollect $filecollect->id, MailToStakeholders try"); 

        return back()->with('success', 'Θα ξεκινήσει η αποστολή των μηνυμάτων. Μπορείτε να παρακολουθήσετε την πρόοδο τους στο log mails');
    }

    public function personal_filecollect_mail(Filecollect $filecollect, Request $request, FilecollectStakeholder $stakeholder){
        $mail = $stakeholder->stakeholder->mail;
        Mail::to($mail)->send(new FilesToUpload($filecollect, $stakeholder, Auth::user()->username));
        Log::channel('mails')->info(Auth::user()->username." Filecollect $filecollect->id, personal MailToStakeholder success: $mail");  

        return back()->with('success', 'Θα ενημερωθεί ο ενδιαφερόμενος. Μπορείτε να παρακολουθήσετε την πρόοδο της αποστολής στο log mails');
    }
}