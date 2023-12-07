<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Month;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Microapp;
use App\Models\Fileshare;
use App\Mail\FilesToReceive;
use Illuminate\Http\Request;
use App\Mail\MicroappToSubmit;
use App\Models\MicroappStakeholder;
use Illuminate\Support\Facades\Log;
use App\Models\FileshareStakeholder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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
        //iterating through identifiers
        foreach($identifiers as $identifier){
            if(School::where('code', trim($identifier))->count()){ //if identifier is school code
                $stakeholder = School::where('code', trim($identifier))->first();
                $found=1;
            }
            else if(Teacher::where('afm', trim($identifier))->count()){//if identifier is teacher afm
                $stakeholder = Teacher::where('afm', trim($identifier))->first();
                $found=1;   
            }
            else{ // if is neither school code nor teacher afm
                array_push($not_found, trim($identifier));
                continue;
            }
            if($my_app=="fileshare"){ //fileshare
                // dd($stakeholder);
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
        }

        Session::put('not_found', $not_found);
        if($found)
            return redirect(url("/".$my_app."_profile/$my_id"))
                ->with('success', "Η ενημέρωση των ενδιαφερόμενων έγινε επιτυχώς");
        else
            return redirect(url("/".$my_app."_profile/$my_id"))
                ->with('warning', "Δεν βρέθηκε σχολείο ή εκπαιδευτικός για να προστεθεί στους ενδιαφερόμενους");   
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
            $stakeholder = Microapp::find($my_id)->stakeholders->first();
            return new MicroappToSubmit($stakeholder);
        }
        else if($my_app=="fileshare"){
            $fileshare = Fileshare::find($my_id);
            $stakeholder = $fileshare->stakeholders->first();
            return new FilesToReceive($fileshare, $stakeholder);
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
        $mail_error=false;
        if($my_app=='fileshare'){
            $fileshare = Fileshare::find($my_id);
            $stakeholders = $fileshare->stakeholders;
            foreach($stakeholders as $stakeholder){
                $mail = $stakeholder->stakeholder->mail;
                try{
                    Mail::to($mail)->send(new FilesToReceive($fileshare, $stakeholder));
                    try{
                        Log::channel('mails')->info("Fileshare $fileshare->id, MailToStakeholders success: $mail");  
                    }
                    catch(Throwable $e){
                    }
                }
                catch(Throwable $e){
                    $mail_error = true;
                    Log::channel('mails')->error("Fileshare $fileshare->id, MailToStakeholders error: $mail ".$e->getMessage());
                }
            }
        }
        else if($my_app=='microapp'){
            $microapp = Microapp::find($my_id);
            $stakeholders = $microapp->stakeholders;  
            foreach($stakeholders as $stakeholder){
                $mail = $stakeholder->stakeholder->mail;
                try{ 
                    Mail::to($mail)->send(new MicroappToSubmit($stakeholder));
                    try{
                        Log::channel('mails')->info("Microapp $microapp->name, MailToStakeholders success: $mail");
                    }
                    catch(Throwable $e){
                    }
                }
                catch(Throwable $e){
                    $mail_error = true;
                    Log::channel('mails')->error("Microapp $microapp->name, MailToStakeholders error: $mail ".$e->getMessage());   
                }
            }
        }
        if(!$mail_error)
            return back()->with('success', 'Ενημερώθηκαν όλοι οι ενδιαφερόμενοι');
        else
            return back()->with('warning', 'Δείτε στο σημερινό log mails ποιοι δεν ενημερώθηκαν'); 
    }

    /**
     * send one email to each of the stakeholders of a microapp that have not submitted an answer
     *
     * @param String $my_app the kind of the app eg fileshare or microapp
     * @param Integer $my_id the id of the microapp
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function send_to_all_that_have_not_submitted($my_app, $my_id){
        $mail_error=false;
        if($my_app=='microapp'){
            $microapp = Microapp::find($my_id);
            $stakeholders = $microapp->stakeholders;  
            foreach($stakeholders as $stakeholder){
                $mail = $stakeholder->stakeholder->mail;
                if($microapp->url == "/all_day_school"){
                    // επειδή το ολοήμερο είναι μηνιαία υποβολή, ενημερώνεται το σχολείο αν δεν έχει υποβάλλει τον τρέχοντα μήνα
                    if(!$stakeholder->stakeholder->all_day_schools->where('month_id', Month::getActiveMonth()->id)->count()){
                        try{
                            Mail::to($mail)->send(new MicroappToSubmit($stakeholder));
                        }
                        catch(Throwable $e){
                            $mail_error=true;
                            Log::channel('mails')->error("Microapp $microapp->name, MailToThoseWhoOwe error: $mail ".$e->getMessage()); 
                        }
                    }
                }
                else{
                    if(!$stakeholder->hasAnswer){
                        try{
                            Mail::to($mail)->send(new MicroappToSubmit($stakeholder));
                        }
                        catch(Throwable $e){
                            $mail_error=true;
                            Log::channel('mails')->error("Microapp $microapp->name, MailToThoseWhoOwe error: $mail ".$e->getMessage());
                        }
                    }  
                }
            }
        }
        if(!$mail_error)
            return back()->with('success', 'Ενημερώθηκαν όσοι ενδιαφερόμενοι δεν έχουν υποβάλλει απάντηση');
        else
            return back()->with('warning', 'Δείτε στο σημερινό log mails ποιοι δεν ενημερώθηκαν');   
    }
}