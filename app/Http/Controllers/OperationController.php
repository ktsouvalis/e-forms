<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Http\Request;
use App\Models\UsersOperations;
use Illuminate\Database\QueryException;

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
                'icon' => $incomingFields['operation_icon'],
                'accepts' => 0,
                'visible' => 0
            ]);
        } 
        catch(QueryException $e){
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
            'user_id'=>1
        ]);
        
         UsersOperations::create([
            'operation_id'=>$record->id,
            'user_id'=>2
        ]); 
        return view('operations',['record'=>$record]);
    }

    public function changeOperationStatus(Request $request){
        if($request->all()['asks_to'] == 'ch_vis_status'){
            $operation = Operation::find($request->all()['operation_id']);
            $operation->visible = $operation->visible==1?0:1;
            $operation->accepts = 0;
            $operation->save();
        }
        if($request->all()['asks_to'] == 'ch_acc_status'){
            $operation = Operation::find($request->all()['operation_id']);
            $operation->accepts = $operation->accepts==1?0:1;
            $operation->save();
        }
        return redirect('/manage_operations');
    }

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
                    $existing_operation =Operation::where('name',$given_name)->first();
                    return view('operation-profile',['dberror'=>"Υπάρχει ήδη χρήστης με username $given_name: $existing_user->display_name, $existing_user->email", 'user' => $operation]);
                }
            }
            else{
                if($operation->isDirty('email')){
                    $given_email = $incomingFields['user_email'];

                    if(User::where('email', $given_email)->count()){
                        $existing_user =User::where('email',$given_email)->first();
                        return view('user-profile',['dberror'=>"Υπάρχει ήδη χρήστης με email $given_email: $existing_user->username, $existing_user->display_name", 'user' => $operation]);

                    }
                }
            }
            $operation->save();
            $edited = true;
        }
        
        // check if an operation has been removed from user
        $user_operations = $user->operations->all();
        
        foreach($user_operations as $one_operation){
            $found=false;
            foreach($request->all() as $key => $value){
                if(substr($key,0,9)=='operation'){
                    if($value == $one_operation->operation_id){
                        $found = true;
                    }
                }
            }
            if(!$found){
                UsersOperations::where('operation_id', $one_operation->operation_id)->where('user_id', $user->id)->first()->delete();
                $edited=true;
            }
        }

        // check if an operation has been added to user
        foreach($request->all() as $key => $value){
            if(substr($key,0,9)=='operation'){
                if(!$user->operations->where('operation_id', $value)->count()){
                    UsersOperations::create([
                        'user_id' => $user->id,
                        'operation_id' => $value
                    ]);
                    $edited = true;
                } 
            }
        }
        
        if(!$edited){
            return view('user-profile',['dberror'=>"Δεν υπάρχουν αλλαγές προς αποθήκευση", 'user' => $user]);
        }
        return redirect("/user_profile/$user->id")->with('success','Επιτυχής αποθήκευση');
    }
}
