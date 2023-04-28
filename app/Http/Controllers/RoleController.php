<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class RoleController extends Controller
{
    //
    public function insertRole(Request $request){
        
        //VALIDATION
        $incomingFields = $request->all();
        $given_name = $incomingFields['role_name3'];
        $given_parent = $incomingFields['role_parent_id3'];

        try{
            $record = Role::create([
                'name' => $incomingFields['role_name3'],
                'parent_id' => $incomingFields['role_parent_id3'],
            ]);
        } 
        catch(QueryException $e){
            return view('roles',['dberror'=>"Κάποιο πρόβλημα προέκυψε κατά την εκτέλεση της εντολής, προσπαθήστε ξανά.", 'old_data'=>$request]);
        }

        return view('roles',['record'=>$record]);
    }
}
