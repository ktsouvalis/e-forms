<?php

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
use App\Http\Controllers\FilecollectController;
use App\Http\Controllers\microapps\FruitsController;
use App\Http\Controllers\microapps\OutingsController;
use App\Http\Controllers\microapps\TicketsController;
use App\Http\Controllers\microapps\WorkPlanController;
use App\Http\Controllers\microapps\ImmigrantsController;
use App\Http\Controllers\microapps\SchoolAreaController;
use App\Http\Controllers\microapps\AllDaySchoolController;
use App\Http\Controllers\microapps\InternalRulesController;
use App\Http\Controllers\microapps\DefibrillatorsController;

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

Route::view('/school_areas', 'public/school_areas');

//ADMIN Routes

Route::get('/admin/{appname}', function($appname){
    $microapp = Microapp::where('url', '/'.$appname)->firstOrFail();
    if($microapp->active){
        return view('microapps.admin.'.$appname,['appname'=>$appname]);
    }
    else{
        return redirect(url('/index_user'))->with('warning', "Η εφαρμογή $microapp->name είναι ανενεργή");
    }
})->middleware('canViewMicroapp');//will throw a 404 if the url does not exist or a 403 if teacher is not in the stakeholders of this microapp

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

Route::get('/school/{md5}', [SchoolController::class, 'login']);

Route::get('/index_school', function(){
    if(Auth::guard('school')->user())
        return view('index_school');
    else
        return view('index');
});

Route::get('/slogout', [SchoolController::class, 'logout']);

Route::get('/school_app/{appname}', function($appname){
    $microapp = Microapp::where('url', '/'.$appname)->firstOrFail();
    if($microapp->visible){
        return view('microapps.school.'.$appname,['appname'=>$appname]);
    }
    else{
        return redirect(url('/index_school'))->with('warning', "Η εφαρμογή $microapp->name είναι ανενεργή");
    }
})->middleware('isSchool')->middleware('canViewMicroapp');//will throw a 404 if the url does not exist or a 403 if school is not in the stakeholders of this microapp

//////// TEACHER ROUTES

Route::view('/teachers','teachers')->middleware('can:viewAny, '.Teacher::class);

Route::view('/import_teachers', 'import-teachers')->middleware("can:upload, ".Teacher::class);

Route::post('/upload_teachers_template', [TeacherController::class, 'importTeachers']);

Route::post('/upload_didaskalia_apousia_template', [TeacherController::class, 'import_didaskalia_apousia']);

Route::view('/preview_teachers_organiki', 'preview-teachers-organiki')->middleware("can:upload, ".Teacher::class);

Route::post('/insert_teachers_organiki', [TeacherController::class, 'insertTeachers']);

Route::get('/index_teacher', function(){
    if(Auth::guard('teacher')->user())
        return view('index_teacher');
    else
        return view('index');
});

Route::get('/teacher/{md5}', [TeacherController::class, 'login']);

Route::get('/tlogout', [TeacherController::class, 'logout']);

Route::get('/teacher_view/{form}',[TeacherController::class,'makeForm'])->middleware('can:view,form');

Route::get('/teacher_app/{appname}', function($appname){
    $microapp = Microapp::where('url', '/'.$appname)->firstOrFail(); 
    if($microapp->active){
        return view('microapps.teacher.'.$appname,['appname'=>$appname]);
    }
    else{
        return redirect(url('/index_teacher'))->with('warning', "Η εφαρμογή $microapp->name είναι ανενεργή");
    }
})->middleware('isTeacher')->middleware('canViewMicroapp');//will throw a 404 if the url does not exist or a 403 if teacher is not in the stakeholders of this microapp

//////// CONSULTANT ROUTES

Route::post('/consultant_app/check_internal_rule/{internal_rule}', [InternalRulesController::class,'check_internal_rule']);

Route::view('/consultants','consultants')->middleware('can:viewAny, '.Consultant::class);

Route::view('/consultant_app/internal_rules','microapps.admin.internal_rules_consultant')->middleware('isConsultant')->middleware('canViewMicroapp');

Route::view('/consultant_app/work_planning','microapps.admin.work_planning_consultant')->middleware('isConsultant')->middleware('canViewMicroapp');

Route::post('/consultant_app/save_work_plan/{yearWeek}', [WorkPlanController::class, 'saveWorkPlan'])->middleware('isConsultant');

Route::post('/consultant_app/extract_work_plan/{yearWeek}', [WorkPlanController::class, 'extractWorkPlan'])->middleware('isConsultant');

Route::get('/consultant/{md5}', [ConsultantController::class, 'login']);

Route::view('/consultant_schools','consultant_schools')->middleware('isConsultant');

Route::view('/consultant_directors','consultant_directors')->middleware('isConsultant');

Route::view('/index_consultant', 'index_consultant'); // auth checking in view

Route::get('/clogout', [ConsultantController::class, 'logout']);

//////// OPERATIONS ROUTES

Route::view('/manage_operations', 'operations')->middleware('boss');

Route::get('/operation_profile/{operation}', function(Operation $operation){
    return view('operation-profile',['operation'=>$operation]);
})->middleware('boss');

Route::post('/save_operation/{operation}', [OperationController::class,'saveProfile']);

Route::post('/insert_operation', [OperationController::class,'insertOperation']);

//////// MICROAPPS ROUTES

Route::view('/microapps', 'microapps')->middleware('auth');

Route::post('/insert_microapp', [MicroappController::class,'insertMicroapp']);

Route::get('/microapp_profile/{microapp}', function(Microapp $microapp){
    if($microapp->active){
        return view('microapp-profile',['microapp'=>$microapp]);
    }
    abort(404);
})->middleware('can:update,microapp' );

Route::post('/save_microapp/{microapp}', [MicroappController::class,'saveProfile']);

Route::post("/change_microapp_status/{microapp}",[MicroappController::class, 'changeMicroappStatus']);

Route::post("/microapp_onoff/{microapp}",[MicroappController::class, 'onOff']);

Route::post("/save_fruits", [FruitsController::class, 'save_fruits'])->middleware('isSchool');

Route::post("/save_school_area/{school}", [SchoolAreaController::class, 'save_school_area'])->middleware('canUpdateSchoolArea');

Route::get("/school_area_profile/{school?}", function(School $school=null){
    if(Auth::check()){
        $blade='admin';
    }
    else if (Auth::guard('school')->check()){
        $blade='school';
    }
    return view("microapps.$blade.school_area_profile", ['school'=>$school]);
})->middleware('canUpdateSchoolArea');

Route::post("/create_ticket",[TicketsController::class, 'create_ticket'])->middleware('isSchool');

Route::get("/ticket_profile/{ticket}", function(Ticket $ticket){
    if(Auth::user()){
        $blade='admin';
    }
    else if (Auth::guard('school')->user()){
        $blade='school';
    }
    return view("microapps.$blade.ticket-profile", ['ticket'=>$ticket]);
})->middleware('canUpdateTicket');

Route::post("/update_ticket/{ticket}", [TicketsController::class, 'update_ticket'])->middleware('canUpdateTicket');

Route::post("/mark_as_resolved/{ticket}",[TicketsController::class, 'mark_as_resolved'])->middleware('canUpdateTicket');

Route::post('/ticket_needed_visit/{ticket}', [TicketsController::class, 'ticket_needed_visit'])->middleware('canUpdateTicket')->middleware('boss');

Route::post('/update-post', [TicketsController::class, 'update_post']);

Route::post('/get_ticket_file/{ticket}/{original_filename}', [TicketsController::class, 'download_file'])->middleware('canUpdateTicket');

Route::post('/microapp_create_ticket/{appname}', [TicketsController::class, 'microapp_create_ticket']);

Route::post('/admin_create_ticket', [TicketsController::class, 'admin_create_ticket'])->middleware('boss');

Route::post('/new_outing',[OutingsController::class, 'new_outing'])->middleware('isSchool');

Route::post('/download_record/{outing}', [OutingsController::class, 'download_record']); //checking Auth inside the method

Route::post('/delete_outing/{outing}', [OutingsController::class,'delete_outing']); //checking Auth inside the method

Route::post('/check_outing/{outing}', [OutingsController::class,'check_outing']); //checking Auth inside the method

Route::get('/outing_profile/{outing}', function(Outing $outing){
    return view('microapps.school.outing-profile',['outing'=>$outing]);
})->middleware('canUpdateOuting');

Route::post('/save_outing_profile/{outing}', [OutingsController::class,'save_outing_profile'])->middleware('canUpdateOuting');

Route::post('/save_all_day_school', [AllDaySchoolController::class, 'post_all_day'])->middleware('isSchool');

Route::post('/dl_all_day_template/{type}', function(Request $request, $type){
    if($type==1)
        $file = 'all_day/oloimero_dim.xlsx';
    else
        $file = 'all_day/oloimero_nip.xlsx';

    
    $response = Storage::disk('local')->download($file);  
    ob_end_clean();
    try{
        return $response;
    }
    catch(Throwable $e){
        return back()->with('failure', 'Δεν ήταν δυνατή η λήψη του αρχείου, προσπαθήστε ξανά');    
    }
});

Route::post('/update_all_day_template/{type}', [AllDaySchoolController::class, 'update_all_day_template'])->middleware('boss');

Route::post('/dl_all_day_file/{all_day_school}', [AllDaySchoolController::class, 'download_file']);

Route::post('/self_update_all_day/{all_day_school}', [AllDaySchoolController::class, 'self_update']);

Route::post('/save_immigrants', [ImmigrantsController::class, 'post_immigrants'])->middleware('isSchool');

Route::post('/dl_immigrants_template', function(Request $request){
    $file = 'immigrants/immigrants.xlsx';
    $response = Storage::disk('local')->download($file);  
    ob_end_clean();
    try{
        return $response;
    }
    catch(Throwable $e){
        return back()->with('failure', 'Δεν ήταν δυνατή η λήψη του αρχείου, προσπαθήστε ξανά');    
    }
});

Route::post('/update_immigrants_template', [ImmigrantsController::class, 'update_immigrants_template'])->middleware('boss');

Route::post('/dl_immigrants_file/{immigrant}', [ImmigrantsController::class, 'download_file']);

Route::post('/dl_defibrillators_document', function(Request $request){
    $file = 'defibrillators/Διαδικασία_Προμήθειας_Απινιδωτών.pdf';
    $response = Storage::disk('local')->download($file);  
    ob_end_clean();
    try{
        return $response;
    }
    catch(Throwable $e){
        return back()->with('failure', 'Δεν ήταν δυνατή η λήψη του αρχείου, προσπαθήστε ξανά');    
    }
});

Route::post('/save_defibrillators', [DefibrillatorsController::class, 'save_defibrillators'])->middleware('isSchool');

Route::post('/dl_defibrillators_file/{defibrillator}', [DefibrillatorsController::class, 'download_file']);

Route::post("/save_internal_rules", [InternalRulesController::class, 'save_internal_rules'])->middleware('isSchool');

Route::post("/upload_director_comments_file/{internal_rule}", [InternalRulesController::class, 'upload_director_comments_file'])->middleware('auth'); //inside method check further

Route::post("/upload_consultant_comments_file/{internal_rule}", [InternalRulesController::class, 'upload_consultant_comments_file'])->middleware('isConsultant');

Route::post("/approve_int_rule/{type}/{internal_rule}", [InternalRulesController::class, 'approve_int_rule']);

Route::post("/upload_director_signed_file/{internal_rule}", [InternalRulesController::class, 'upload_director_signed_file']); //inside method check further

Route::post("/upload_consultant_signed_file/{internal_rule}", [InternalRulesController::class, 'upload_consultant_signed_file'])->middleware('isConsultant');

Route::post("/dl_internal_rules_file/{internal_rule}/{file_type}", [InternalRulesController::class, 'download_int_rule_file']);

Route::post('/check_internal_rule/{internal_rule}', [InternalRulesController::class,'check_internal_rule']);


// FILECOLLECTS ROUTES

Route::view('/filecollects', 'filecollects')->middleware('auth');

Route::post('/insert_filecollect', [FilecollectController::class, 'insert_filecollect'])->middleware('auth');

Route::get('/filecollect_profile/{filecollect}', function(Filecollect $filecollect){
    return view('filecollect-profile', ['filecollect'=> $filecollect]);
})->middleware('can:view,filecollect');

Route::post('/filecollect_save/{filecollect}', [FilecollectController::class,'saveProfile'])->middleware('can:view,filecollect');

Route::post('/dl_filecollect_file/{filecollect}/{type}', function(Filecollect $filecollect, $type){
    if($type == 'base'){
        $filename = $filecollect->base_file;
    }
    else if($type=='template'){
        $filename = $filecollect->template_file;
    }
    $file = "file_collects/".$filecollect->id."/".$filename;
    if(Storage::disk('local')->exists($file)){
        $response = Storage::disk('local')->download($file);  
        ob_end_clean();
        try{
            return $response;
        }
        catch(\Exception $e){
            return back()->with('failure', 'Δεν ήταν δυνατή η λήψη του αρχείου, προσπαθήστε ξανά');    
        }
    } 
    else 
        return back()->with('failure', 'Το αρχείο δεν υπάρχει.');  
});//->middleware('can:view,filecollect');

Route::post('/dl_stake_file/{old_data}', [FilecollectController::class,'getSchoolFile']);

Route::post('/delete_stake_file/{stakeholder}', [FilecollectController::class,'delete_stakeholder_file']);

Route::post('/update_filecollect_file/{filecollect}/{type}', [FilecollectController::class, 'update_file'])->middleware('can:view,filecollect');

Route::post('/update_filecollect_comment/{filecollect}', [FilecollectController::class, 'update_comment'])->middleware('can:view,filecollect');

Route::post('/change_filecollect_status/{filecollect}', [FilecollectController::class, 'changeFilecollectStatus'])->middleware('can:view,filecollect');

Route::get("/teacher_filecollect/{filecollect}", function(Filecollect $filecollect){
    $stakeholder = $filecollect->stakeholders->where('stakeholder_id', Auth::guard('teacher')->id())->where('stakeholder_type', 'App\Models\Teacher')->first();
    if(!$stakeholder or !$filecollect->visible){
        abort(403);
    }
    return view('teacher-filecollect', ['filecollect' => $filecollect]);
})->middleware('isTeacher');

Route::get("/school_filecollect/{filecollect}", function(Filecollect $filecollect){
    $stakeholder = $filecollect->stakeholders->where('stakeholder_id', Auth::guard('school')->id())->where('stakeholder_type', 'App\Models\School')->first();
    if(!$stakeholder or !$filecollect->visible){
        abort(403);
    }
    return view('school-filecollect', ['filecollect' => $filecollect]);
})->middleware('isSchool');

Route::post("/post_filecollect/{filecollect}", [FilecollectController::class, 'post_filecollect']);

Route::post("/filecollect_checked/{stakeholder}",[FilecollectController::class, 'check_uncheck']); //access is checked inside the controller
// ->middleware('can:check,filecollectStakeholder');

Route::post("/delete_filecollect/{filecollect}", [FilecollectController::class, 'delete_filecollect'])->middleware('can:view,filecollect');

Route::post("/save_filecollect_stake_comment/{stakeholder}",[FilecollectController::class, 'save_filecollect_comment']);//access is checked inside the controller

Route::post("/download_filecollect_directory/{filecollect}", [FilecollectController::class, 'download_filecollect_directory'])->middleware('can:view,filecollect');


// FILESHARES ROUTES

Route::get("/teacher_fileshare/{fileshare}", function(Fileshare $fileshare){
    $stakeholder = $fileshare->stakeholders->where('stakeholder_id', Auth::guard('teacher')->id())->where('stakeholder_type', 'App\Models\Teacher')->first();
    if(!$stakeholder){
        abort(403);
    }
    $stakeholder->visited_fileshare=true;
    $stakeholder->save();
    return view('teacher-fileshare', ['fileshare' => $fileshare]);
})->middleware('isTeacher');

Route::get("/school_fileshare/{fileshare}", function(Fileshare $fileshare){
    $stakeholder = $fileshare->stakeholders->where('stakeholder_id', Auth::guard('school')->id())->where('stakeholder_type', 'App\Models\School')->first();
    if(!$stakeholder){
        abort(403);
    }
    $stakeholder->visited_fileshare=true;
    $stakeholder->save();
    return view('school-fileshare', ['fileshare' => $fileshare]);
})->middleware('isSchool');

Route::view('/fileshares', 'fileshares')->middleware('auth');

Route::post('/insert_fileshare', [FileshareController::class, 'insert_fileshare']);

Route::post('/fileshare_save/{fileshare}', [FileshareController::class, 'update_fileshare']);

Route::get('/fileshare_profile/{fileshare}', function(Fileshare $fileshare){
    return view('fileshare-profile', ['fileshare'=> $fileshare]);
})->middleware('can:view,fileshare');

Route::post('/save_fileshare_comment/{fileshare}', [FileshareController::class, 'add_comment']);

Route::post("/delete_fileshare/{fileshare}", [FileshareController::class, 'delete_fileshare']);

Route::post("/get_file/{fileshare}/{original_filename}", [FileshareController::class, 'download_file']);

Route::post("/del_file/{fileshare}/{orginal_filename}", [FileshareController::class, 'delete_file'])->middleware('can:view,fileshare');

Route::post('auto_update_fileshare_whocan/{fileshare}', [FileshareController::class, 'auto_update_whocan']);

Route::post('/inform_my_teachers/{fileshare}', [FileshareController::class, 'school_informs_teachers']);

Route::post('/fileshare_allow_schools/{fileshare}', [FileshareController::class, 'allow_schools']);


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

    Route::view('/month','month');

    Route::post('/set_active_month', [MonthController::class,'setActiveMonth']);
});

Route::group(['middleware' => 'can:changeVirtualMonth,'.Operation::class], function () {

    Route::view('/virtual_month','virtual_month');

    Route::post('/set_vmonth/{school}', [MonthController::class,'setVirtualMonth']);

    Route::post('/reset_active_month/{school}', [MonthController::class,'resetActiveMonth']);
});

//  COMMANDS Routes

Route::group(['middleware' => "can:executeCommands," .Operation::class], function () {

    Route::get('/commands', function(){
        $maintenanceMode = app()->isDownForMaintenance();
        return view('commands', ['maintenanceMode' => $maintenanceMode]);
    });

    Route::post('/com_change_active_month', function () {
        Artisan::call('change-active-month');
        $output = session()->get('command_output');
        try{
            Log::channel('commands_executed')->info(Auth::user()->username.": ".$output);
        }
        catch(Throwable $e){
    
        }
        return redirect(url('/commands'))->with('command', $output);
    });

    Route::post('/com_eDirecorate_update', function () {
        Artisan::call('update-e-directorate');
        $output = session()->get('command_output');
        try{
            Log::channel('commands_executed')->info(Auth::user()->username.": ".$output);
        }
        catch(Throwable $e){
    
        }
        return redirect(url('/commands'))->with('command', $output);
    });

    Route::post('/com_change_microapp_accept_status', function () {
        Artisan::call('microapps:accept_not');
        $output = session()->get('command_output');
        try{
            Log::channel('commands_executed')->info(Auth::user()->username.": ".$output);
        }
        catch(Throwable $e){
    
        }
        return redirect(url('/commands'))->with('command', $output);
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
        return redirect(url('/commands'))->with('command',$output);
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
        return redirect(url('/'))->with('success', 'Maintenance Mode OFF');
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
            Log::channel('user_memorable_actions')->error(Auth::user()->username." failed to backup db ".$e->getMessage());
            return back()->with('failure', $e->getMessage());
        }

        Log::channel('user_memorable_actions')->info(Auth::user()->username." successfully backup db");
        ob_end_clean();
        try {
            return response()->download(storage_path('app/' . $backupFilePath))->deleteFileAfterSend();
        }
        catch(\Exception $e) {
            Log::channel('files')->error(Auth::user()->username." failed to backup db ".$e->getMessage());
            return back()->with('failure', $e->getMessage());
        }
    });

    Route::post('/update_app', function(){
        $sudoRequired = env('SUDO_REQUIRED');
        if($sudoRequired){
            $fetch = Process::path('/opt/e-forms')->run('git fetch');
            if($fetch->successful()){
                Log::channel('commands_executed')->info(Auth::user()->username.":git fetch: ".$fetch->output());
                $pull = Process::path('/opt/e-forms')->run('git pull');
                if($pull->successful()){
                    Log::channel('commands_executed')->info(Auth::user()->username.":git pull: ".$pull->output());
                    $migrate = Process::path('/opt/e-forms')->run('php artisan migrate');
                    if($migrate->successful()){
                        Log::channel('commands_executed')->info(Auth::user()->username.":php artisan migrate ".$migrate->output());
                        return back()->with('success', 'Επιτυχής ενημέρωση της εφαρμογής');
                    }
                    else{
                        Log::channel('commands_executed')->error(Auth::user()->username.":php artisan migrate ".$migrate->errorOutput());
                        return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων');
                    }
                }
                else{
                    Log::channel('commands_executed')->error(Auth::user()->username.":git pull ".$pull->errorOutput());
                    return back()->with('failure', 'Αποτυχία ενημέρωσης κώδικα');
                }
            }    
            else{
                Log::channel('commands_executed')->error(Auth::user()->username.":git fetch ".$fetch->errorOutput());
                return back()->with('failure', 'Αποτυχία ενημέρωσης της εφαρμογής');
            }
        }
        else{
            return back()->with('warning', 'Production mode only');
        }
    });
});

//Sections Routes

Route::view('/sections', 'sections')->middleware('auth')->middleware('can:viewSections, '. School::class);

Route::post('/upload_sections_template', [SectionController::class, 'import_sections']);

Route::post('/delete_sections', [SectionController::class, function(Request $request){
    Section::truncate();
    return redirect(url('/sections'))->with('success', 'Τα τμήματα διαγράφηκαν');
}]);

//misc routes

Route::view('/anaplirotes','anaplirotes');