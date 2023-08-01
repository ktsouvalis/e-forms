<?php

use App\Models\User;
use App\Models\School;
use App\Mail\ShareLink;
use App\Models\Teacher;
use App\Models\Microapp;
use App\Models\Fileshare;
use App\Models\Operation;
use Illuminate\Http\Request;
use App\Mail\MicroappToSubmit;
use App\Models\MicroappStakeholder;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\WhocanController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\MicroappController;
use App\Http\Controllers\FileshareController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\microapps\FruitsController;

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

Route::get('/back', function(){
    return back();
});

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

Route::view('/directors', 'directors')->middleware('auth')->middleware('can:viewAny, '. School::class);

Route::post('/upload_schools_template', [SchoolController::class, 'importSchools']);

Route::post('/upload_directors_template', [SchoolController::class, 'importDirectors']);

Route::view('/import_schools', "import-schools")->middleware('can:upload, '.School::class);

Route::view('/import_directors', "import-directors")->middleware('can:upload, '.School::class);

Route::view('/preview_schools', "preview-schools")->middleware('can:upload, '. School::class);

Route::view('/preview_directors', "preview-directors")->middleware('can:upload, '. School::class);

Route::post('/insert_schools', [SchoolController::class, 'insertSchools']);

Route::post('/insert_directors', [SchoolController::class, 'insertDirectors']);

Route::get('/school/{md5}', [SchoolController::class, 'login']);

Route::view('/index_school', 'index_school'); // auth checking in view

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

Route::view('/teachers','teachers')->middleware('auth')->middleware('can:viewAny, '. Teacher::class);

Route::view('/import_teachers', 'import-teachers')->middleware("can:upload, ".Teacher::class);

Route::post('/upload_teachers_template', [TeacherController::class, 'importTeachers']);

Route::view('/preview_teachers_organiki', 'preview-teachers-organiki')->middleware("can:upload, ".Teacher::class);

Route::post('/insert_teachers_organiki', [TeacherController::class, 'insertTeachers']);

Route::view('/index_teacher', 'index_teacher'); // auth checking in view

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

Route::post("/save_fruits/{school}", [FruitsController::class, 'save_fruits']);

// FILESHARES ROUTES

Route::get("/teacher_fileshare/{teacher}", function(Teacher $teacher){
    if(Auth::guard('teacher')->id()!=$teacher->id){
        abort(403);
    }
    return view('teacher-fileshare', ['teacher' => $teacher]);
});

Route::get("/school_fileshare/{school}", function(School $school){
    if(Auth::guard('school')->id()!=$school->id){
        abort(403);
    }
    return view('school-fileshare', ['school' => $school]);
});

Route::view('/fileshares', 'fileshares')->middleware('auth');

Route::post('/insert_fileshare', [FileshareController::class, 'insert_fileshare']);

Route::post('/fileshare_save/{fileshare}', [FileshareController::class, 'update_fileshare']);

Route::get('/fileshare_profile/{fileshare}', function(Fileshare $fileshare){
    return view('fileshare-profile', ['fileshare'=> $fileshare]);
})->middleware('can:view,fileshare');

Route::post("/delete_fileshare/{fileshare}", [FileshareController::class, 'delete_fileshare']);

Route::post("/dl_file/{fileshare}", [FileshareController::class, 'download_file']);

Route::post("/x_file/{fileshare}", [FileshareController::class, 'delete_file']);

//ADMIN Routes

Route::view('/', 'index')->name('index');

Route::get('/admin/{appname}', function($appname){
    $microapp = Microapp::where('url', '/'.$appname)->firstOrFail();
    if($microapp->active){
        return view('microapps.admin.'.$appname,['appname'=>$appname]);
    }
    else{
        return redirect(url('/index'))->with('warning', "Η εφαρμογή $microapp->name είναι ανενεργή");
    }
})->middleware('canViewMicroapp');//will throw a 404 if the url does not exist or a 403 if teacher is not in the stakeholders of this microapp



// WHOCAN Routes

Route::post("/delete_all_whocans/{my_app}/{my_id}", [WhocanController::class, 'delete_all_whocans']);

Route::post("/delete_one_whocan/{my_app}/{my_id}", [WhocanController::class, 'delete_one_whocan']);

Route::post('/import_whocan/{my_app}/{my_id}', [WhocanController::class, 'import_whocans']);

// MAIL Routes
Route::post("/send_mail_all_whocans/{my_app}/{my_id}", [WhocanController::class, 'send_to_all']);

Route::post("/send_to_those_whocans_without_answer/{my_app}/{my_id}", [WhocanController::class, 'send_to_all_that_have_not_submitted']);

Route::get('/preview_mail_all_whocans/{my_app}/{my_id}', [WhocanController::class,'preview_mail_to_all']);

Route::post("share_link/{type}/{my_id}", function($type, $my_id){
    if($type=="school"){
        $school = School::findOrFail($my_id);
        Mail::to($school->mail)->send(new ShareLink('school', $school->md5));
    }
    else{
        $teacher = Teacher::findOrFail($my_id);
        Mail::to($teacher->mail)->send(new ShareLink('teacher', $teacher->md5));
    }

    return back()->with('success', 'Ο σύνδεσμος στάλθηκε επιτυχώς');
});

//MONTH Routes

Route::group(['middleware' => 'can:changeActiveMonth,'.Operation::class], function () {

    Route::view('/month','month');

    Route::post('/set_active_month', [MonthController::class,'setActiveMonth']);
});

//  COMMANDS Routes

Route::group(['middleware' => "can:executeCommands," .Operation::class], function () {

    Route::view('/commands', 'commands');

    Route::post('/com_change_active_month', function () {
        Artisan::call('change-active-month');
        return redirect(url('/commands'))->with('success', 'Η εντολή εκτελέστηκε επιτυχώς');
    });

    Route::post('/com_change_microapp_accept_status', function () {
        Artisan::call('microapps:accept_not');
        return redirect(url('/commands'))->with('success', 'Η εντολή εκτελέστηκε επιτυχώς');
    });

    Route::post('/com_edit_super_admins', function (Request $request) {
        $username = $request->input('username');
        Artisan::call('super', ['u_n' => $username]);
        return redirect(url('/commands'))->with('success', 'Η εντολή εκτελέστηκε επιτυχώς');
    });
});