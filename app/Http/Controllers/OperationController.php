<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Operation;
use Illuminate\Http\Request;
use App\Models\UsersOperations;


class OperationController extends Controller
{
    //
    public function insertOperation(Request $request){
        
        $incomingFields = $request->all();
        try{
            $record = Operation::create([
                'name' => $incomingFields['operation_name'],
                'url' => $incomingFields['operation_url'],
                'color' => $incomingFields['operation_color'],
                'icon' => $incomingFields['operation_icon']
            ]);
        } 
        catch(Throwable $e){
            return view('operations',['dberror'=>"Κάποιο πρόβλημα προέκυψε κατά την εκτέλεση της εντολής, προσπαθήστε ξανά.", 'old_data'=>$request]);
        }

        foreach($request->all() as $key=>$value){
            if(substr($key,0,4)=='user'){
                UsersOperations::create([
                    'operation_id'=>$record->id,
                    'user_id'=>$value
                ]); 
            }
        }

        UsersOperations::create([
            'operation_id'=>$record->id,
            'user_id'=>1,
            'can_edit' => 1
        ]);
        
         UsersOperations::create([
            'operation_id'=>$record->id,
            'user_id'=>2,
            'can_edit' => 1
        ]); 
        return view('operations',['record'=>$record]);
    }

    // public function changeOperationStatus(Request $request){
    //     if($request->all()['asks_to'] == 'ch_vis_status'){
    //         $operation = Operation::find($request->all()['operation_id']);
    //         $operation->visible = $operation->visible==1?0:1;
    //         $operation->accepts = 0;
    //         $operation->save();
    //     }
    //     if($request->all()['asks_to'] == 'ch_acc_status'){
    //         $operation = Operation::find($request->all()['operation_id']);
    //         $operation->accepts = $operation->accepts==1?0:1;
    //         $operation->save();
    //     }
    //     return redirect(url('/manage_operations'));
    // }

    public function saveProfile(Operation $operation, Request $request){

        $incomingFields = $request->all();
       
        $operation->name = $incomingFields['name'];
        $operation->url = $incomingFields['url'];
        $operation->color = $incomingFields['color'];
        $operation->icon = $incomingFields['icon'];
        $edited=false;
        // check if changes happened to operation table
        if($operation->isDirty()){
            if($operation->isDirty('name')){
                $given_name = $incomingFields['name'];

                if(Operation::where('name', $given_name)->count()){
                    return redirect(url("/operation_profile/$operation->id"))->with('failure',"Υπάρχει ήδη λειτουργία με name $given_name.");
                }
            }
            else{
                if($operation->isDirty('url')){
                    $given_url = $incomingFields['url'];

                    if(Operation::where('url', $given_url)->count()){
                        $existing_operation =Operation::where('url',$given_url)->first();
                        return redirect(url("/operation_profile/$operation->id"))->with('failure',"Υπάρχει ήδη λειτουργία με url $given_url: $existing_operation->name");
                    }
                }
            }
            $operation->save();
            $edited = true;
        }
        
        // check if a user has been removed from operation
        $operation_users = $operation->users->all();
        
        foreach($operation_users as $one_user){
            $found=false;
            foreach($request->all() as $key => $value){
                if(substr($key,0,4)=='user'){
                    if($value == $one_user->user_id){
                        $found = true;
                    }
                }
            }
            if(!$found){
                UsersOperations::where('user_id', $one_user->user_id)->where('operation_id', $operation->id)->first()->delete();
                $edited=true;
            }
        }

        // check if a user has been added to operation
        foreach($request->all() as $key => $value){
            if(substr($key,0,4)=='user'){
                if(!$operation->users->where('user_id', $value)->count()){
                    UsersOperations::create([
                        'operation_id' => $operation->id,
                        'user_id' => $value
                    ]);
                    $edited = true;
                } 
            }
        }
        
        if(!$edited){
            return redirect(url("/operation_profile/$operation->id"))->with('warning',"Δεν υπάρχουν αλλαγές προς αποθήκευση");
        }
        return redirect(url("/operation_profile/$operation->id"))->with('success','Επιτυχής αποθήκευση');
    }
}
