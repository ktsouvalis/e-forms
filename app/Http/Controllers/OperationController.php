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
}
