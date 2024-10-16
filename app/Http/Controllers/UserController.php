<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsersOperations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController extends Controller
{
    public function index(){
        return view('manage.users.index');
    }

    public function edit(User $user){
        return view('manage.users.edit')->with('user', $user);
    }
    public function login(Request $request){
        
        $incomingFields=$request->validate([//the html names of the fields
            'username'=>'required',
            'password'=>'required'
        ]);
        if(auth()->attempt(['username'=>$incomingFields['username'], 'password'=>$incomingFields['password']])){
            $request->session()->regenerate();
            return redirect(url('/index_user'))->with('success','Συνδεθήκατε επιτυχώς');
        }
        else{
            return redirect(url('/index_user'))->with('failure', 'Λάθος όνομα χρήστη ή κωδικός πρόσβασης');
        }
    }

    public function logout(Request $request){
        // $request->session()->flush(); OR
        auth()->logout();
        return redirect(url('/index_user'))->with('success','Αποσυνδεθήκατε...');
    }

    public function passwordChange(Request $request){
        $incomingFields = $request->all();
        $rules = [
            'pass1' => 'min:6|same:pass1_confirmation',
            'pass1_confirmation' => 'min:6'
        ];
        $validator = Validator::make($incomingFields, $rules);
        if($validator->fails()){
            return back()
                ->with('failure',$validator->errors()->first());
        }
        $user = Auth::user();

        $user->password = bcrypt($incomingFields['pass1']);
        $user->save();

        return redirect(url('/index_user'))->with('success', 'Ο νέος σας κωδικός αποθηκεύτηκε επιτυχώς');
    }

    public function passwordReset(Request $request, User $user){
        $user->password = bcrypt('123456');
        $user->save();
        
        return back()->with('success',"Ο κωδικός του χρήστη $user->username άλλαξε επιτυχώς");
    }

    
    public function store(Request $request){
        //VALIDATION
        $incomingFields = $request->all();

        $given_name = $incomingFields['user_name3'];
        // $given_email = $incomingFields['user_email3'];

        if(User::where('username', $given_name)->count()){
            $existing_user = User::where('username', $given_name)->first();
            return back()
                ->with('failure', "Υπάρχει ήδη χρήστης με όνομα χρήστη $given_name: $existing_user->display_name, $existing_user->email")
                ->with('old_data', $incomingFields);
        }

        if($incomingFields['user_department3']=="Επιλογή τμήματος"){
            return back()
                ->with('failure', "Πρέπει να επιλέξετε τμήμα")
                ->with('old_data', $incomingFields);   
        }
        
        //VALIDATION PASSED

        try{
            $record = User::create([
                'username' => $incomingFields['user_name3'],
                'display_name' => $incomingFields['user_display_name3'],
                'email' => $incomingFields['user_email3'],
                'password' => bcrypt($incomingFields['user_password3']),
                'telephone' => $incomingFields["user_telephone3"],
                'department_id' => $incomingFields["user_department3"]
            ]);
        } 
        catch(QueryException $e){
            return back()
                ->with('failure', "Κάποιο πρόβλημα προέκυψε κατά την εκτέλεση της εντολής, προσπαθήστε ξανά.")
                ->with('old_data', $incomingFields);
        }

        foreach($request->all() as $key=>$value){
            if(substr($key,0,9)=='operation'){
                UsersOperations::create([
                    'user_id' => $record->id,
                    'operation_id' => $value,
                    'can_edit' => 0
                ]); 
            }
        }

        return back()
            ->with('success','Επιτυχής καταχώρηση νέου χρήστη')
            ->with('record', $record);
    }

    public function update(User $user, Request $request){

        $incomingFields = $request->all();
       
        $user->username = $incomingFields['user_name'];
        $user->display_name = $incomingFields['user_display_name'];
        $user->email = $incomingFields['user_email'];
        $user->telephone = $incomingFields['user_telephone'];
        $user->department_id = $incomingFields['user_department'];
        $edited=false;
        // check if changes happened to user table
        if($user->isDirty()){
            if($user->isDirty('username')){
                $given_name = $incomingFields['user_name'];

                if(User::where('username', $given_name)->count()){
                    $existing_user =User::where('username',$given_name)->first();
                    return back()->with('failure',"Υπάρχει ήδη χρήστης με username $given_name: $existing_user->display_name, $existing_user->email");
                }
            }
            
            $user->save();
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
                        'operation_id' => $value,
                        'can_edit' => 0 // !!!must be checked from the ui!!!!
                    ]);
                    $edited = true;
                } 
            }
        }
        
        if(!$edited){
            // return view('user-profile',['dberror'=>"Δεν υπάρχουν αλλαγές προς αποθήκευση", 'user' => $user]);
            return back()->with('warning',"Δεν υπάρχουν αλλαγές προς αποθήκευση");
        }
        return back()->with('success','Επιτυχής αποθήκευση');
    }

    public function destroy(User $user){
        $user->delete();
        return back()->with('success',"Ο χρήστης $user->username διαγράφηκε επιτυχώς");
    }
}