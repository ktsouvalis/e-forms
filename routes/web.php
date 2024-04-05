<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\School;
use App\Mail\ShareLink;
use App\Models\Section;
use App\Models\Teacher;
use App\Models\Microapp;
use App\Models\Fileshare;
use App\Models\Operation;
use App\Models\Consultant;
use App\Models\Filecollect;
use Illuminate\Http\Request;
use App\Mail\MicroappToSubmit;
use App\Models\microapps\Outing;
use App\Models\microapps\Ticket;
use App\Models\MicroappStakeholder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Models\microapps\InternalRule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\WhocanController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\MicroappController;
use App\Http\Controllers\FileshareController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\microapps\EnrollmentController;
use App\Http\Controllers\FilecollectController;
use App\Http\Controllers\microapps\FruitsController;
use App\Http\Controllers\microapps\OutingsController;
use App\Http\Controllers\microapps\TicketsController;
use App\Http\Controllers\microapps\WorkPlanController;
use App\Http\Controllers\microapps\ImmigrantsController;
use App\Http\Controllers\microapps\SchoolAreaController;
use App\Http\Controllers\microapps\AllDaySchoolController;
use App\Http\Controllers\microapps\InternalRulesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
///// INDEX ////////////////////////////////////

Route::view('/', 'index')->name('index');

Route::view('/index_user', 'index_user');

Route::get('/back', function(){
    return back();
});

Route::post('/find_entity', function(Request $request){
    if(!is_numeric($request->entity_code))
        return redirect(url('/'))->with('warning', "Θα πρέπει να καταχωρίσετε αριθητική τιμή.");
    switch (strlen($request->entity_code)) {
        case 6:
            $teacher=App\Models\Teacher::where('am', $request->entity_code)->first();
            if($teacher) {
                return redirect(url("/share_link/teacher/$teacher->id"));
            }else {
                return redirect(url('/'))->with('warning', "Δε βρέθηκε Εκπαιδευτικός της Δ/νσης Π.Ε. Αχαΐας με αυτά τα στοιχεία.");
            }
        break;
        case 7:
            $school=App\Models\School::where('code', $request->entity_code)->first();
            if($school) {
                return redirect(url("/share_link/school/$school->id"));
            }else {
                return redirect(url('/'))->with('warning', "Δε βρέθηκε Σχολική Μονάδα της Δ/νσης Π.Ε. Αχαΐας με αυτό τον Κωδικό Υ.ΠΑΙ.Θ.Α.");
            }
        break;
        case 9:
            $teacher=App\Models\Teacher::where('afm', $request->entity_code)->first();
            if($teacher) {
                return redirect(url("/share_link/teacher/$teacher->id"));
            }else {
                return redirect(url('/'))->with('warning', "Δε βρέθηκε Εκπαιδευτικός της Δ/νσης Π.Ε. Αχαΐας με αυτά τα στοιχεία.");
            }
        break;
        default:
        return redirect(url('/'))->with('warning', "Θα πρέπει να καταχωρίσετε ΑΜ/ΑΦΜ για Εκπαιδευτικό, Κωδικό Υ.ΠΑΙ.Θ.Α. για Σχολείο.");
        break;
    }
}

);

//PUBLIC ROUTES

Route::view('/school_areas', 'public/school_areas')->name('school_areas_public');

//// USER ROUTES

Route::post('/login', [UserController::class,'login'])->middleware('guest');

Route::get('/logout',[UserController::class, 'logout'])->middleware('auth');

Route::view('/change_password', 'password_change_form')->middleware('auth');

Route::post('/change_password', [UserController::class, 'passwordChange']);

Route::view('/manage_users', 'users')->middleware('boss');

Route::post('/upload_user_template', [UserController::class, 'importUsers'])->name('upload_user_template');

Route::post('/insert_users', [UserController::class, 'insertUsers']);

Route::post('/insert_user', [UserController::class,'insertUser']);

Route::get('/user_profile/{user}', function(User $user){
    return view('user-profile',['user'=>$user]);
})->middleware('boss');

Route::post('/save_user/{user}', [UserController::class,'saveProfile']);

Route::post('/reset_password/{user}', [UserController::class, 'passwordReset']);

//////// SCHOOL ROUTES

Route::view('/schools', 'schools')->middleware('auth')->middleware('can:viewAny, '. School::class);

Route::view('/directors', 'directors')->middleware('auth')->middleware('can:viewDirectors, '. School::class);

Route::post('/upload_schools_template', [SchoolController::class, 'importSchools']);

Route::post('/upload_directors_template', [SchoolController::class, 'importDirectors']);

Route::view('/import_schools', "import-schools")->middleware('can:upload, '.School::class);

Route::view('/import_directors', "import-directors")->middleware('can:updateDirectors, '.School::class);

Route::view('/preview_schools', "preview-schools")->middleware('can:upload, '. School::class);

Route::view('/preview_directors', "preview-directors")->middleware('can:updateDirectors, '. School::class);

Route::post('/insert_schools', [SchoolController::class, 'insertSchools']);

Route::post('/insert_directors', [SchoolController::class, 'insertDirectors']);

Route::get('/school/{md5}', [SchoolController::class, 'login'])->name('school_login');

Route::get('/index_school', function(){
        return view('index_school');
});

Route::get('/slogout', [SchoolController::class, 'logout']);

//////// TEACHER ROUTES

Route::view('/teachers','teachers')->middleware('can:viewAny, '.Teacher::class);

Route::view('/import_teachers', 'import-teachers')->middleware("can:upload, ".Teacher::class);

Route::post('/upload_teachers_template', [TeacherController::class, 'importTeachers']);

Route::post('/upload_didaskalia_apousia_template', [TeacherController::class, 'import_didaskalia_apousia']);

Route::view('/preview_teachers_organiki', 'preview-teachers-organiki')->middleware("can:upload, ".Teacher::class);

Route::post('/insert_teachers_organiki', [TeacherController::class, 'insertTeachers']);

Route::get('/index_teacher', function(){
    return view('index_teacher');
});

Route::get('/teacher/{md5}', [TeacherController::class, 'login']);

Route::get('/tlogout', [TeacherController::class, 'logout']);

//////// CONSULTANT ROUTES
Route::view('/consultants','consultants')->middleware('can:viewAny, '.Consultant::class);

Route::view('/consultant_evaluation','consultant_evaluation')->middleware('isConsultant');

Route::get('/consultant/{md5}', [ConsultantController::class, 'login']);

Route::view('/consultant_schools','consultant_schools')->middleware('isConsultant');

Route::view('/consultant_teachers','consultant_teachers')->middleware('isConsultant');

Route::view('/index_consultant', 'index_consultant'); // auth checking in view

Route::get('/clogout', [ConsultantController::class, 'logout']);

/// EVALUATION ROUTES
Route::view('/evaluation', 'evaluation');

Route::view('/evaluation_differences', 'evaluation_differences');

//////// MANAGE OPERATIONS ROUTES
Route::resource('manage/operations', OperationController::class)->middleware('boss');

Route::group(['prefix' =>'manage/operations'], function(){
    Route::post('/set_menu_priority', [OperationController::class,'setMenuPriority']);
});

//////// MANAGE MICROAPPS ROUTES
Route::resource('manage/microapps', MicroappController::class);

Route::group(['prefix' => 'manage/microapps'], function(){ 
    Route::post("/change_microapp_status/{microapp}",[MicroappController::class, 'changeMicroappStatus']);

    Route::post("/microapp_onoff/{microapp}",[MicroappController::class, 'onOff']);
});

//ENROLLMENTS ROUTES
Route::resource('enrollments', EnrollmentController::class)->middleware('canViewMicroapp');

Route::group(['prefix' => 'enrollments', 'middleware' => 'canViewMicroapp'], function () {
    Route::post("/{select}", [EnrollmentController::class, 'save'])->name('enrollments.save');

    Route::post("/upload_file/{upload_file_name}", [EnrollmentController::class, 'upload_file'])->name('enrollments.upload_file');

    Route::get("/{file}/{download_file_name}", [EnrollmentController::class, 'download_file'])->name('enrollments.download_file');
});

// FRUITS ROUTES
Route::resource('fruits', FruitsController::class)->middleware('canViewMicroapp');

// SCHOOL AREA ROUTES
Route::resource('school_area', SchoolAreaController::class)->middleware('canViewMicroapp');

// TICKETS ROUTES
Route::resource('tickets', TicketsController::class)->middleware('canViewMicroapp');

Route::group(['prefix' => 'tickets', 'middleware' =>'canViewMicroapp'], function(){
    Route::post('/ticket_needed_visit/{ticket}', [TicketsController::class, 'ticket_needed_visit'])->name('tickets.visit')->middleware('boss');

    Route::post('/update-post/{ticket}', [TicketsController::class, 'update_post'])->name('tickets.update_post')->middleware('canUpdateTicket');

    Route::post("/mark_as_resolved/{ticket}", [TicketsController::class, 'mark_as_resolved'])->name('tickets.mark_as_resolved')->middleware('canUpdateTicket');

    Route::get('/get_ticket_file/{ticket}/{original_filename}', [TicketsController::class, 'download_file'])->name('tickets.download_file')->middleware('canUpdateTicket');

    Route::post('/admin_create_ticket', [TicketsController::class, 'admin_create_ticket'])->name('tickets.admin_create_ticket')->middleware('boss');

    Route::post('/microapp_create_ticket/{appname}', [TicketsController::class, 'microapp_create_ticket'])->name('tickets.microapp_create_ticket');
});

// OUTINGS ROUTES
Route::resource('outings', OutingsController::class)->middleware('canViewMicroapp');

Route::group(['prefix' => 'outings', 'middleware' => 'canViewMicroapp'], function () {
    Route::get('/download_file/{outing}', [OutingsController::class, 'download_file'])->name('outings.download_file'); //checking access inside the method

    Route::post('/check/{outing}', [OutingsController::class,'check_outing'])->name('outings.check'); //checking access inside the method

    Route::post('/count_sections/{outing}', [OutingsController::class,'count_sections'])->name('outings.count_sections')->middleware('auth');
});

// ALL_DAY_SCHOOL ROUTES
Route::resource('all_day_school', AllDaySchoolController::class)->middleware('canViewMicroapp');

Route::group(['prefix' => 'all_day_school', 'middleware' => 'canViewMicroapp'], function () {
    Route::get('/download_template/{type}', [AllDaySchoolController::class, 'download_template'])->name('all_day_school.download_template');

    Route::post('/update_template/{type}', [AllDaySchoolController::class, 'update_all_day_template'])->name('all_day_school.update_template')->middleware('boss');

    Route::get('/download_file/{all_day_school}', [AllDaySchoolController::class, 'download_file'])->name('all_day_school.download_file'); //access rights are checked inside the method
});

// IMMIGRANTS ROUTES
Route::resource('immigrants', ImmigrantsController::class)->middleware('canViewMicroapp');

Route::group(['prefix' => 'immigrants', 'middleware' => 'canViewMicroapp'], function () {
    Route::get('/download_template/yes', [ImmigrantsController::class, 'download_template'])->name('immigrants.download_template');
    //i need the /yes in the url because without it there is conflict with the show() method of the resource route

    Route::post('/update_template', [ImmigrantsController::class, 'update_template'])->name('immigrants.update_template')->middleware('boss');

    Route::get('/download_file/{immigrant}', [ImmigrantsController::class, 'download_file'])->name('immigrants.download_file'); //access rights are checked inside the method
});

// INTERNAL RULES ROUTES
Route::resource('internal_rules', InternalRulesController::class)->middleware('canViewMicroapp');

Route::group(['prefix' => 'internal_rules', 'middleware' => 'canViewMicroapp'], function () {
    Route::post("/upload_director_comments_file/{internal_rule}", [InternalRulesController::class, 'upload_director_comments_file'])->name('internal_rules.upload_director_comments_file')->middleware('auth');

    Route::post("/upload_consultant_comments_file/{internal_rule}", [InternalRulesController::class, 'upload_consultant_comments_file'])->name('internal_rules.upload_consultant_comments_file')->middleware('isConsultant');

    Route::post("/upload_director_signed_file/{internal_rule}", [InternalRulesController::class, 'upload_director_signed_file'])->name('internal_rules.upload_director_signed_file')->middleware('auth');

    Route::post("/upload_consultant_signed_file/{internal_rule}", [InternalRulesController::class, 'upload_consultant_signed_file'])->name('internal_rules.upload_consultant_signed_file')->middleware('isConsultant');

    Route::get("/download_file/{internal_rule}/{file_type}", [InternalRulesController::class, 'download_file'])->name('internal_rules.download_file'); //access rights are checked inside the method

    Route::post('/check/{internal_rule}', [InternalRulesController::class,'check'])->name('internal_rules.check'); //access rights are checked inside the method
});

// WORK PLAN ROUTES
Route::resource('work_planning', WorkPlanController::class)->middleware('canViewMicroapp');

Route::group(['prefix' => 'work_planning', 'middleware'=>'canViewMicroapp'], function () {
    
    Route::post('/save_work_plan/{yearWeek}', [WorkPlanController::class, 'saveWorkPlan'])->name('work_planning.save_work_plan')->middleware('isConsultant');

    Route::post('/extract_work_plan/{yearWeek}', [WorkPlanController::class, 'extractWorkPlan'])->name('work_planning.extract_work_plan')->middleware('isConsultant');
});

// FILECOLLECTS ROUTES
Route::resource('filecollects', FilecollectController::class);

Route::group(['prefix' => 'filecollects'], function () {
    Route::get('/download_admin_file/{filecollect}/{type}', [FilecollectController::class, 'download_admin_file']);

    Route::get('/download_stake_file/{old_data}', [FilecollectController::class,'download_stake_file']);

    Route::post('/delete_stake_file/{stakeholder}', [FilecollectController::class,'delete_stakeholder_file']);

    Route::post('/update_admin_file/{filecollect}/{type}', [FilecollectController::class, 'update_admin_file'])->middleware('can:view,filecollect');

    Route::post('/update_comment/{filecollect}', [FilecollectController::class, 'update_comment'])->middleware('can:view,filecollect');

    Route::post('/change_status/{filecollect}', [FilecollectController::class, 'change_status'])->middleware('can:view,filecollect');

    Route::post("/upload_stake_file/{filecollect}", [FilecollectController::class, 'upload_stake_file']);

    Route::post("/filecollect_checked/{stakeholder}",[FilecollectController::class, 'check_uncheck']); //access is checked inside the controller

    Route::post("/save_stake_comment/{stakeholder}",[FilecollectController::class, 'save_stake_comment']);//access is checked inside the controller

    Route::post("/download_directory/{filecollect}", [FilecollectController::class, 'download_directory'])->middleware('can:view,filecollect');

    Route::post('/num_of_lines/{filecollect}', [FilecollectController::class, 'add_num_of_lines'])->middleware('boss');

    Route::post("/extract_xlsx_file/{filecollect}", [FilecollectController::class, 'extract_xlsx_file'])->middleware('boss');

    Route::post("/send_personal_message", [FilecollectController::class, 'send_personal_message']); //the stakeholder goes to the backenmd through a hidden input
});

// FILESHARES ROUTES
Route::resource('fileshares', FileshareController::class);

Route::group(['prefix' => 'fileshares'], function(){
    Route::post('/save_comment/{fileshare}', [FileshareController::class, 'save_comment']);

    Route::get("/download_file/{fileshare}/{original_filename}", [FileshareController::class, 'download_file']);

    Route::post("/delete_file/{fileshare}/{orginal_filename}", [FileshareController::class, 'delete_file'])->middleware('can:view,fileshare');

    Route::post('/auto_update_whocan/{fileshare}', [FileshareController::class, 'auto_update_whocan']);

    Route::post('/inform_my_teachers/{fileshare}', [FileshareController::class, 'school_informs_teachers']);

    Route::post('/allow_schools/{fileshare}', [FileshareController::class, 'allow_schools']);
});

// WHOCAN Routes

Route::post("/delete_all_whocans/{my_app}/{my_id}", [WhocanController::class, 'delete_all_whocans']);

Route::post("/delete_one_whocan/{my_app}/{my_id}", [WhocanController::class, 'delete_one_whocan']);

Route::post('/import_whocan/{my_app}/{my_id}', [WhocanController::class, 'import_whocans']);


// MAIL Routes

Route::post("/send_mail_all_whocans/{my_app}/{my_id}", [WhocanController::class, 'send_to_all']);

Route::post("/send_to_those_whocans_without_answer/{my_app}/{my_id}", [WhocanController::class, 'send_to_all_that_have_not_submitted']);

Route::get('/preview_mail_all_whocans/{my_app}/{my_id}', [WhocanController::class,'preview_mail_to_all']);

Route::post('/mail_not_visited/{fileshare}',[WhocanController::class, 'mail_to_those_who_not_visited_fileshare'])->middleware('can:view,fileshare');

Route::post('/mail_visited/{fileshare}',[WhocanController::class, 'mail_to_those_who_visited_fileshare'])->middleware('can:view,fileshare');

Route::post('/fileshare_personal_mail/{fileshare}/{stakeholder}',[WhocanController::class, 'personal_fileshare_mail'])->middleware('can:view,fileshare');

Route::post('/mail_submitted/{filecollect}',[WhocanController::class, 'mail_to_those_who_uploaded_filecollect'])->middleware('can:view,filecollect');

Route::post('/mail_not_submitted/{filecollect}',[WhocanController::class, 'mail_to_those_who_not_uploaded_filecollect'])->middleware('can:view,filecollect');

Route::post('/filecollect_personal_mail/{filecollect}/{stakeholder}',[WhocanController::class, 'personal_filecollect_mail'])->middleware('can:view,filecollect');

Route::match(array('GET','post'), "/share_link/{type}/{my_id}", function($type, $my_id){
    if($type=="school"){
        $school = School::findOrFail($my_id);
    }
    else if ($type=="teacher"){
        $teacher = Teacher::findOrFail($my_id);
    }
    else if($type=="consultant"){
        $consultant = Consultant::findOrFail($my_id);
    }
    try{
        Mail::to($$type->mail)->send(new ShareLink($type, $$type->md5));  //if $type is 'school', $$type is $school etc
    }
    catch(\Exception $e){
        try{
            Log::channel('mails')->error("Σύνδεσμος δεν στάλθηκε προσωπικά στο ".$$type->mail.": ".$e->getMessage());
        }
        catch(\Exception $e){
    
        }
        return back()->with('warning', 'Η αποστολή δεν έγινε και τα σφάλματα που καταγράφηκαν στο Log mails');
    }  
    try{
        Log::channel('mails')->info("Σύνδεσμος στάλθηκε προσωπικά στο ".$$type->mail);
    }
    catch(\Exception $e){
        
    }
    $mail_address = $$type->mail;
    $first_letter = substr($mail_address, 0 ,1);
    $at_position = strpos($mail_address, '@');
    $rest_of_address = substr($mail_address, $at_position-1);
    $mail_to_show = $first_letter.'*****'.$rest_of_address;
    return back()->with('success', 'Ο σύνδεσμος στάλθηκε στο '.$mail_to_show);
});

Route::post("share_links_to_all/{type}", function($type){
    $error=false;
    if($type=="school"){
        $school = School::where('sent_link_mail', false)->take(70)->get();
        // dd($school);
    }
    else if ($type=="teacher"){
        $teacher = Teacher::where('sent_link_mail', false)->take(70)->get();
    }
    else if($type=="consultant"){
        $consultant = Consultant::all();
    }
    if($$type->count()){
        foreach($$type as $one){
            $error_personal=false;
            try{
                // Log::channel('mails')->info("Μαζική αποστολή: Σύνδεσμος στάλθηκε στο ".$one->mail);
                Mail::to($one->mail)->send(new ShareLink($type, $one->md5));   
            }
            catch(\Exception $e){
                try{
                    Log::channel('mails')->error("Μαζική αποστολή: Σύνδεσμος δεν στάλθηκε στο ".$one->mail.": ".$e->getMessage());
                }
                catch(\Exception $e){
        
                }
                $error=true;
                $error_personal=true;
            }
            if(!$error_personal){
                if($type!="consultant"){
                    $one->sent_link_mail=true;
                    $one->save();
                }
                try{
                    Log::channel('mails')->info("Μαζική αποστολή: Σύνδεσμος στάλθηκε στο ".$one->mail);
                }
                catch(\Exception $e){
        
                }
            }
        }
    }
    else{
        return back()->with('warning', 'Δεν υπάρχουν περισσότεροι χρήστες προς αποστολή συνδέσμου');
    }
    if($error){
        return back()->with('warning', 'Η αποστολή έγινε με σφάλματα που καταγράφηκαν στο Log mails');
    }
    return back()->with('success', 'Οι σύνδεσμοι στάλθηκαν επιτυχώς');
});

Route::post("/reset_links_to_all/{type}", function($type){
    if($type=="schools"){
        School::query()->update(['sent_link_mail' => false]);
    }
    else if ($type=="teachers"){
        Teacher::query()->update(['sent_link_mail' => false]);
    }
    return back()->with('success', 'All links have been reset successfully');
});

//MONTH Routes

Route::group(['middleware' => 'can:changeActiveMonth,'.Operation::class], function () {
    Route::post('/set_active_month', [MonthController::class,'setActiveMonth']);
});

Route::group(['middleware' => 'can:changeVirtualMonth,'.Operation::class], function () {

    Route::view('/virtual_month','virtual_month');

    Route::post('/set_vmonth/{school}', [MonthController::class,'setVirtualMonth']);

    Route::post('/reset_active_month/{school}', [MonthController::class,'resetActiveMonth']);
});

//  COMMANDS Routes

Route::group(['middleware' => "can:executeCommands," .Operation::class], function () {

    Route::post('/com_change_active_month', function () {
        Artisan::call('change-active-month');
        $output = session()->get('command_output');
        try{
            Log::channel('commands_executed')->info(Auth::user()->username.": ".$output);
        }
        catch(Throwable $e){
    
        }
        return back()->with('command', $output);
    });

    Route::post('/com_eDirecorate_update', function () {
        Artisan::call('update-e-directorate');
        $output = session()->get('command_output');
        try{
            Log::channel('commands_executed')->info(Auth::user()->username.": ".$output);
        }
        catch(Throwable $e){
    
        }
        return back()->with('command', $output);
    });

    Route::post('/com_change_microapp_accept_status', function () {
        Artisan::call('microapps:accept_not');
        $output = session()->get('command_output');
        try{
            Log::channel('commands_executed')->info(Auth::user()->username.": ".$output);
        }
        catch(Throwable $e){
    
        }
        return back()->with('command', $output);
    });

    Route::post('/com_edit_super_admins', function (Request $request) {
        $username = $request->input('username');
        Artisan::call('super', ['u_n' => $username]);
        $output = session()->get('command_output');
        try{
            Log::channel('commands_executed')->info(Auth::user()->username.": ".$output);
        }
        catch(Throwable $e){
    
        }
        return back()->with('command',$output);
    });

    Route::post('/app_down', function (Request $request){
        $secret_parameter = $request->input('secret');
        Artisan::call("down",['--secret'=>$secret_parameter]);
        try{
            Log::channel('commands_executed')->info(Auth::user()->username.": Maintenance mode ON");
        }
        catch(Throwable $e){
    
        }
        return redirect(url("/$secret_parameter"))->with('success', 'Maintenance Mode ON');
    });

    Route::post('/app_up', function (Request $request){
        Artisan::call("up");
        try{
            Log::channel('commands_executed')->info(Auth::user()->username.": Maintenance mode OFF");
        }
        catch(Throwable $e){
    
        }
        return back()->with('success', 'Maintenance Mode OFF');
    });

    Route::post('/com_directorate_name_update', function(Request $request){
        $name_parameter = $request->input('dir_name');
        Artisan::call("app:udn",['d_n'=> $name_parameter]);
        $output = session()->get('command_output');
        try{
            Log::channel('commands_executed')->info(Auth::user()->username.": ".$output);   
        }
        catch(\Exception $e){

        }
        return back()->with('command',$output);
    });

    Route::post('/com_directorate_code_update', function(Request $request){
        $code_parameter = $request->input('dir_code');
        Artisan::call("app:udc",['d_c'=> $code_parameter]);
        $output = session()->get('command_output');
        try{
            Log::channel('commands_executed')->info(Auth::user()->username.": ".$output);   
        }
        catch(\Exception $e){

        }
        return back()->with('command',$output);
    });

    Route::post('/db_backup', function(Request $request){
        $host = env('DB_HOST');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $database = env('DB_DATABASE');
        $backupFilePath = date('Y-m-d_H-i-s') . '_backup.sql';
        $sudoRequired = env('SUDO_REQUIRED');
        $command = "mysqldump -h $host -u $username -p$password --databases $database > " . storage_path('app/' . $backupFilePath);
        try{
             exec($command);
        }
        catch(\Exception $e){
            try{
                Log::channel('user_memorable_actions')->error(Auth::user()->username." failed to backup db ".$e->getMessage());
            }
            catch(\Exception $e){
    
            }
            return back()->with('failure', $e->getMessage());
        }
        try{
            Log::channel('user_memorable_actions')->info(Auth::user()->username." successfully backup db");
        }
        catch(\Exception $e){
    
        }
        ob_end_clean();
        try {
            return response()->download(storage_path('app/' . $backupFilePath))->deleteFileAfterSend();
        }
        catch(\Exception $e) {
            try{
                Log::channel('files')->error(Auth::user()->username." failed to backup db ".$e->getMessage());
            }
            catch(\Exception $e){
            }
            return back()->with('failure', $e->getMessage());
        }
    });

    Route::post('/update_app', function(){
        $sudoRequired = env('SUDO_REQUIRED');
        if($sudoRequired){
            $fetch = Process::path('/opt/e-forms')->run('git fetch');
            if($fetch->successful()){
                try{
                    Log::channel('commands_executed')->info(Auth::user()->username.":git fetch: ".$fetch->output());
                }
                catch(\Exception $e){
        
                }
                $pull = Process::path('/opt/e-forms')->run('git pull');
                if($pull->successful()){
                    try{
                        Log::channel('commands_executed')->info(Auth::user()->username.":git pull: ".$pull->output());
                    }
                    catch(\Exception $e){
        
                    }
                    $migrate = Artisan::call('migrate');
                    if ($migrate === 0) {
                        try{
                            Log::channel('commands_executed')->info(Auth::user()->username.":migrate ".Artisan::output());
                        }
                        catch(\Exception $e){
        
                        }
                        return back()->with('success', 'Επιτυχής ενημέρωση της εφαρμογής');
                    }
                    else{
                        try{
                            Log::channel('commands_executed')->error(Auth::user()->username.":migrate ".Artisan::output());
                        }
                        catch(\Exception $e){
        
                        }
                        return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων');
                    }
                }
                else{
                    try{
                        Log::channel('commands_executed')->error(Auth::user()->username.":git pull ".$pull->errorOutput());
                    }
                    catch(\Exception $e){
        
                    }
                    return back()->with('failure', 'Αποτυχία ενημέρωσης κώδικα');
                }
            }    
            else{
                try{
                    Log::channel('commands_executed')->error(Auth::user()->username.":git fetch ".$fetch->errorOutput());
                }
                catch(\Exception $e){
        
                }
                return back()->with('failure', 'Αποτυχία ενημέρωσης της εφαρμογής');
            }
        }
        else{
            return back()->with('warning', 'Production mode only');
        }
    });

    Route::get('/get_logs', function(Request $request){
        $date = $request->query('date');
        $formattedDate = Carbon::parse($date)->format('Y-m-d');
        $logDirectory = storage_path('logs');
        $logFiles = glob($logDirectory . '/*' . $formattedDate . '.log');
        if (empty($logFiles)) {
            return back()->with('failure', 'Δεν υπάρχουν αρχεία καταγραφής για την επιλεγμένη ημερομηνία');
        }
    
        $zipFile = tempnam(sys_get_temp_dir(), 'logs') . '.zip';
        $zip = new ZipArchive;
        if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
            return back()->with('failure', 'Αποτυχία δημιουργίας αρχείου zip');
        }
    
        foreach ($logFiles as $logFile) {
            $zip->addFile($logFile, basename($logFile));
        }
    
        if ($zip->close() !== TRUE) {
            return back()->with('failure', 'Αποτυχία κλεισίματος αρχείου zip');
        }
    
        if (!file_exists($zipFile)) {
            return back()->with('failure', 'Δεν υπάρχει το αρχείο zip');
        }
        $filename = 'logs_' . $formattedDate . '.zip';
        ob_end_clean();
        return response()->download($zipFile, $filename)->deleteFileAfterSend();
    });
});

//Sections Routes

Route::view('/sections', 'sections')->middleware('auth')->middleware('can:viewSections, '. School::class);

Route::post('/upload_sections_template', [SectionController::class, 'import_sections']);

// Route::post('/delete_sections', [SectionController::class, 'delete_sections']);