<?php

namespace App\Http\Controllers;

use App\Models\Microapp;
use App\Models\MicroappUser;
use Illuminate\Http\Request;

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
        
        // check if a user has been removed from microapp
        $microapp_users = $microapp->users->all();
        
        foreach($microapp_users as $one_user){
            $found=false;
            foreach($request->all() as $key => $value){
                if(substr($key,0,4)=='user'){
                    if($value == $one_user->user_id){
                        $found = true;
                    }
                }
            }
            if(!$found){
                MicroappUser::where('user_id', $one_user->user_id)->where('microapp_id', $microapp->id)->first()->delete();
                $edited=true;
            }
        }

        // check if a user has been added to microapp
        foreach($request->all() as $key => $value){
            if(substr($key,0,4)=='user'){
                if(!$microapp->users->where('user_id', $value)->count()){
                    MicroappUser::create([
                        'microapp_id' => $microapp->id,
                        'user_id' => $value,
                        'can_edit' => 1 // !!!must be checked from the ui!!!!
                    ]);
                    $edited = true;
                } 
            }
        }
        
        if(!$edited){
            return redirect(url("/microapp_profile/$microapp->id"))->with('warning',"Δεν υπάρχουν αλλαγές προς αποθήκευση");
        }
        return redirect(url("/microapp_profile/$microapp->id"))->with('success','Επιτυχής αποθήκευση');
    }
}
