<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Operation;
use Illuminate\Http\Request;
use App\Models\UsersOperations;

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
        } catch (Throwable $e) {
            return view('operations', [
                'dberror' => "Κάποιο πρόβλημα προέκυψε κατά την εκτέλεση της εντολής, προσπαθήστε ξανά.",
                'old_data' => $request
            ]);
        }

        // check the input if there is a user added to the operation. can_edit is 0 until something else is implemented
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == 'user') {
                UsersOperations::create([
                    'operation_id' => $record->id,
                    'user_id' => $value,
                    'can_edit' => 0
                ]);
            }
        }

        return view('operations', ['record' => $record]);
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
        
        // Check if a user has been removed from the operation
        $operation_users = $operation->users->all();
        
        foreach ($operation_users as $one_user) {
            $found = false;
            
            foreach ($request->all() as $key => $value) {
                if (substr($key, 0, 4) == 'user') {
                    if ($value == $one_user->user_id) {
                        $found = true;
                    }
                }
            }
            
            if (!$found) {
                UsersOperations::where('user_id', $one_user->user_id)
                    ->where('operation_id', $operation->id)
                    ->first()
                    ->delete();
                $edited = true;
            }
        }

        // Check if a user has been added to the operation
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == 'user') {
                if (!$operation->users->where('user_id', $value)->count()) {
                    UsersOperations::create([
                        'operation_id' => $operation->id,
                        'user_id' => $value,
                        'can_edit' => 0 // !!!must be checked from the UI!!!!
                    ]);
                    $edited = true;
                } 
            }
        }

        if (!$edited) {
            return redirect(url("/operation_profile/$operation->id"))
                ->with('warning', "Δεν υπάρχουν αλλαγές προς αποθήκευση");
        }

        return redirect(url("/operation_profile/$operation->id"))
            ->with('success', 'Επιτυχής αποθήκευση');
    }
}
