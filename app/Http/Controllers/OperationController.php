<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Operation;
use Illuminate\Http\Request;
use App\Models\UsersOperations;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class OperationController extends Controller
{
    /**
     * Insert a new operation into the database.
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\View\View The rendered view.
     */
    public function insertOperation(Request $request)
    {
        $incomingFields = $request->all();
        
        // create an operation record 
        try {
            $record = Operation::create([
                'name' => $incomingFields['operation_name'],
                'url' => $incomingFields['operation_url'],
                'color' => $incomingFields['operation_color'],
                'icon' => $incomingFields['operation_icon']
            ]);
            Log::channel('user_memorable_actions')->info(Auth::user()->username." insertOperation ". $record->name);
        } 
        catch (Throwable $e) {
            Log::channel('throwable_db')->error(Auth::user()->username." insertOperation");
            return redirect(url('/manage_operations'))
                ->with('failure', "Κάποιο πρόβλημα προέκυψε κατά την εκτέλεση της εντολής, προσπαθήστε ξανά.")
                ->with('old_data', $request->all());
        }

        //check if some other users except the admins are coming in from the input and add these records to the users_operations table
        foreach($request->all() as $key=>$value){
            if(substr($key,0,4)=='user'){
                $user_id=$value;
                if(isset($incomingFields['edit'.$user_id])){
                    $can_edit = $incomingFields['edit'.$user_id]=='no'?0:1;
                }
                UsersOperations::create([
                    'operation_id'=>$record->id,
                    'user_id'=>$user_id,
                    'can_edit'=> $can_edit
                ]);
            }
        }

        return redirect(url('/manage_operations'))->with('success', 'Τα στοιχεία της λειτουργίας καταχωρήθηκαν επιτυχώς');

    }

    /**
     * Save the profile of an operation.
     *
     * @param Operation $operation The operation model instance.
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function saveProfile(Operation $operation, Request $request)
    {
        $incomingFields = $request->all();
       
        $operation->name = $incomingFields['name'];
        $operation->url = $incomingFields['url'];
        $operation->color = $incomingFields['color'];
        $operation->icon = $incomingFields['icon'];
        $edited = false;

        // Check if changes happened to the operation table
        if ($operation->isDirty()) {
            //if name has changed
            if ($operation->isDirty('name')) {
                $given_name = $incomingFields['name'];

                if (Operation::where('name', $given_name)->count()) {
                    //if there is already an operation with the newly given name
                    return redirect(url("/operation_profile/$operation->id"))
                        ->with('failure', "Υπάρχει ήδη λειτουργία με name $given_name.");
                }
            } else {
                // if url has changed
                if ($operation->isDirty('url')) {
                    $given_url = $incomingFields['url'];

                    if (Operation::where('url', $given_url)->count()) {
                        //if there is already an operation with the newly given url
                        $existing_operation = Operation::where('url', $given_url)->first();
                        return redirect(url("/operation_profile/$operation->id"))
                            ->with('failure', "Υπάρχει ήδη λειτουργία με url $given_url: $existing_operation->name");
                    }
                }
            }

            $operation->save();
            $edited = true;
        }
        
        if($request->user()->can('addUser', Operation::class)){
            // everything is going to be deleted from the microapps_users table and rewriten
            $old_records = $operation->users;
            $operation->users()->delete();

            //check if some other users except the admins are coming in from the input and add these records to the microapps_users table
            foreach($request->all() as $key=>$value){
                if(substr($key,0,4)=='user'){ //checks if some user's checkbox is checked
                    $user_id=$value;
                    if(isset($incomingFields['edit'.$user_id])){ //checks if the radio buttons and their values come as excpected 
                        $can_edit = $incomingFields['edit'.$user_id]=='no'?0:1; //for a new user in microapp checks radiobuttons
                    }
                    else{
                        $can_edit = $old_records->where('user_id', $user_id)->first()->can_edit; // for an existing user with no changes in radios
                    }
                    UsersOperations::create([
                        'operation_id' => $operation->id,
                        'user_id' => $user_id,
                        'can_edit' => $can_edit
                    ]);
                }
            }
        }
        return redirect(url("/operation_profile/$operation->id"))
            ->with('success', 'Επιτυχής αποθήκευση');
    }
}
