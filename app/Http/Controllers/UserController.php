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
    public function login(Request $request){
        
        $incomingFields=$request->validate([//the html names of the fields
            'username'=>'required',
            'password'=>'required'
        ]);
        if(auth()->attempt(['username'=>$incomingFields['username'], 'password'=>$incomingFields['password']])){
            $request->session()->regenerate();
            return redirect(url('/'))->with('success','Συνδεθήκατε επιτυχώς');
        }
        else{
            return redirect(url('/'))->with('failure', 'Λάθος όνομα χρήστη ή κωδικός πρόσβασης');
        }
    }

    public function logout(Request $request){
        // $request->session()->flush(); OR
        auth()->logout();
        return redirect(url('/'))->with('success','Αποσυνδεθήκατε...');
    }

    public function passwordChange(Request $request){
        $incomingFields = $request->all();
        $rules = [
            'pass1' => 'min:6|required_with:pass1_confirmation|same:pass1_confirmation',
            'pass1_confirmation' => 'min:6'
        ];
        $validator = Validator::make($incomingFields, $rules);
        if($validator->fails()){
            return redirect(url('/password_reset'))->with('failure', 'Οι κωδικοί πρέπει να ταιριάζουν και να είναι 6+ χαρακτήρες');
        }
        $user = User::find(Auth::id());

        $user->password = bcrypt($incomingFields['pass1']);
        $user->save();

        return redirect(url('/'))->with('success', 'Ο νέος σας κωδικός αποθηκεύτηκε επιτυχώς');
    }

    public function passwordReset(Request $request, User $user){
        $user->password = bcrypt('123456');
        $user->save();
        
        return back()->with('success',"Ο κωδικός του χρήστη $user->username άλλαξε επιτυχώς");
    }

    public function importUsers(Request $request){
        $rule = [
            'import_users' => 'required|mimes:xlsx'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return redirect(url('/'))->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
            
        }
        $filename = "users_file_".Auth::id().".xlsx"; 
        $path = $request->file('import_users')->storeAs('files', $filename);
        $mime = Storage::mimeType($path);
        $spreadsheet = IOFactory::load("../storage/app/$path");
        $users_array=array();
        $row=2;
        $error=0;
        $rowSumValue="1";
        while ($rowSumValue != "" && $row<10000){
            $check=array();
            $check['username'] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();
            $check['display_name']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2, $row)->getValue();
            $check['email']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(3, $row)->getValue();
            $check['password']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(4, $row)->getValue();

            if($check['username']=='' or $check['username']==null){
                $error = 1; 
                $check['username']="Κενό πεδίο";
            }
            else{
                if(User::where('username', $check['username'])->count()){
                    $error = 1;
                    $check['username']="Υπάρχει ήδη το username";
                }
            }

            $rule = [
                'display_name' => 'required'
            ];
            $validator = Validator::make($check, $rule);
            if($validator->fails()){ 
                $error=1;
                $check['display_name']="Κενό πεδίο";
            }
            
            if($check['email']=='' or $check['email']==null){
                $error = 1; 
                $check['email']="Κενό πεδίο";
            }
            else{
                if(User::where('email', $check['email'])->count()){
                    $error = 1;
                    $check['email']="Υπάρχει ήδη το email";
                }
            }

            $rule = [
                'password' => 'required'
            ];
            $validator = Validator::make($check, $rule);
            if($validator->fails()){ 
                $error=1;
                $check['password']="Κενό πεδίο";
            }
           
            array_push($users_array, $check);
            $row++;
            $rowSumValue="";
            for($col=1;$col<=5;$col++){
                $rowSumValue .= $spreadsheet->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();   
            }
        }
        
        if($error){
            return view('users',['users_array'=>$users_array,'active_tab'=>'import', 'asks_to'=>'error']);
        }
        else{
            session(['ysers' => $users_array]);
            return view('users',['users_array'=>$users_array,'active_tab'=>'import', 'asks_to'=>'save']);
        }
    }

    public function insertUsers(){
        $users_array = session('ysers');
        $imported=0;
        foreach($users_array as $one_user){
            $user = new User();
            $user->username = $one_user['name'];
            $user->display_name = $one_user['display_name'];
            $user->email = $one_user['email'];
            $user->password = bcrypt($one_user['password']);
            try{
                $imported++;
                $user->save();
            } 
            catch(QueryException $e){
                return view('users',['dberror2'=>"Κάποιο πρόβλημα προέκυψε, προσπαθήστε ξανά.", 'active_tab'=>'import']);
            }
        }
        session()->forget('ysers');
        return redirect(url('/users'))->with('success', "Η εισαγωγή $imported χρηστών ολοκληρώθηκε");
    }

    public function insertUser(Request $request){
        //VALIDATION
        $incomingFields = $request->all();
        $given_name = $incomingFields['user_name3'];
        // $given_email = $incomingFields['user_email3'];

        if(User::where('username', $given_name)->count()){
            $existing_user = User::where('username', $given_name)->first();
            return redirect(url('/manage_users'))
                ->with('failure', "Υπάρχει ήδη χρήστης με όνομα χρήστη $given_name: $existing_user->display_name, $existing_user->email")
                ->with('old_data', $incomingFields);
        }
        // else{
        //     if(User::where('email', $given_email)->count()){
        //         $existing_user = User::where('email', $given_email)->first();
        //         return redirect(url('/manage_users'))
        //             ->with('failure', "Υπάρχει ήδη χρήστης με όνομα χρήστη $given_email: $existing_user->username, $existing_user->display_name")
        //             ->with('old_data', $incomingFields);
        //     }
        // }
        //VALIDATION PASSED

        try{
            $record = User::create([
                'username' => $incomingFields['user_name3'],
                'display_name' => $incomingFields['user_display_name3'],
                'email' => $incomingFields['user_email3'],
                'password' => bcrypt($incomingFields['user_password3']),
                'telephone' => $incomingFields["user_telephone3"]
            ]);
        } 
        catch(QueryException $e){
            return redirect(url('/manage_users'))
                ->with('failure', "Κάποιο πρόβλημα προέκυψε κατά την εκτέλεση της εντολής, προσπαθήστε ξανά.")
                ->with('old_data', $incomingFields);
        }

        foreach($request->all() as $key=>$value){
            if(substr($key,0,9)=='operation'){
                UsersOperations::create([
                    'user_id'=>$record->id,
                    'operation_id'=>$value,
                    'can_edit' =>0
                ]); 
            }
        }

        return redirect(url('/manage_users'))
            ->with('success','Επιτυχής καταχώρηση νέου χρήστη')
            ->with('record', $record);
    }

    public function saveProfile(User $user, Request $request){

        $incomingFields = $request->all();
       
        $user->username = $incomingFields['user_name'];
        $user->display_name = $incomingFields['user_display_name'];
        $user->email = $incomingFields['user_email'];
        $user->telephone = $incomingFields['user_telephone'];
        $edited=false;
        // check if changes happened to user table
        if($user->isDirty()){
            if($user->isDirty('username')){
                $given_name = $incomingFields['user_name'];

                if(User::where('username', $given_name)->count()){
                    $existing_user =User::where('username',$given_name)->first();
                    return redirect(url("/user_profile/$user->id"))->with('failure',"Υπάρχει ήδη χρήστης με username $given_name: $existing_user->display_name, $existing_user->email");
                }
            }
            // else{
            //     if($user->isDirty('email')){
            //         $given_email = $incomingFields['user_email'];

            //         if(User::where('email', $given_email)->count()){
            //             $existing_user =User::where('email',$given_email)->first();
            //             return redirect(url("/user_profile/$user->id"))->with('failure',"Υπάρχει ήδη χρήστης με email $given_email: $existing_user->username, $existing_user->display_name");

            //         }
            //     }
            // }
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
            return redirect(url("/user_profile/$user->id"))->with('warning',"Δεν υπάρχουν αλλαγές προς αποθήκευση");
        }
        return redirect(url("/user_profile/$user->id"))->with('success','Επιτυχής αποθήκευση');
    }

    public function usersDl(){
        
        $users = User::all();
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        
        $activeWorksheet->setCellValue('A1', 'Username');
        $activeWorksheet->setCellValue('B1', 'DisplayName');
        $activeWorksheet->setCellValue('C1', 'email');
        
        $row = 2;
        foreach($users as $user){
            
            $activeWorksheet->setCellValue("A".$row, $user->username);
            $activeWorksheet->setCellValue("B".$row, $user->display_name);
            $activeWorksheet->setCellValue("C".$row, $user->email);
            
            $row++;
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = "usersTo_".date('YMd')."_".Auth::id().".xlsx";
        $writer->save($filename);

        return response()->download("$filename");
    }
}
