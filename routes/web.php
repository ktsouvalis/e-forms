<?php

use App\Models\User;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Microapp;
use App\Models\Fileshare;
use App\Models\Operation;
use App\Policies\FormPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AllDayController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\WhocanController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\MicroappController;
use App\Http\Controllers\FileshareController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\EndDocumentsController;

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

Route::post('/change_password', [UserController::class, 'passwordChange'])->middleware('auth');

Route::view('/manage_users', 'users')->middleware('boss');

Route::post('/upload_user_template', [UserController::class, 'importUsers'])->name('upload_user_template');

Route::post('/insert_users', [UserController::class, 'insertUsers']);

Route::post('/insert_user', [UserController::class,'insertUser']);

Route::get('/user_profile/{user}', function(User $user){
    return view('user-profile',['user'=>$user]);
})->middleware('boss');

Route::post('/save_user/{user}', [UserController::class,'saveProfile']);

Route::post('/reset_password/{user}', [UserController::class, 'passwordReset'])->middleware('auth');

//////// SCHOOL ROUTES

Route::view('/schools', 'schools')->middleware('auth');

Route::post('/upload_schools_template', [SchoolController::class, 'importSchools']);

Route::view('/import_schools', "import-schools")->middleware('can:upload, '.School::class);

Route::view('/preview_schools', "preview-schools")->middleware('can:upload, '. School::class);

Route::post('/insert_schools', [SchoolController::class, 'insertSchools']);

Route::get('/school/{md5}', [SchoolController::class, 'login']);

Route::view('/index_school', 'index_school'); // auth checking in view

Route::get('/slogout', [SchoolController::class, 'logout']);

Route::get('/school_app/{appname}', function($appname){
    $microapp = Microapp::where('url', '/'.$appname)->first(); //there is one result because if there wasn't the middleware would throw 404
    if($microapp->active){
        return view('microapps.school.'.$appname,['appname'=>$appname]);
    }
    else{
        return redirect(url('/index_school'))->with('warning', "Η εφαρμογή $microapp->name είναι ανενεργή");
    }
})->middleware('canViewMicroapp');//will throw a 404 if the url does not exist or a 403 if school is not in the stakeholders of this microapp

//////// TEACHER ROUTES

Route::view('/teachers','teachers')->middleware('auth');

Route::view('/import_teachers', 'import-teachers')->middleware("can:create, ".Teacher::class);

Route::post('/upload_teachers_organiki_template', [TeacherController::class, 'importTeachersOrganiki']);

Route::view('/preview_teachers_organiki', 'preview-teachers-organiki')->middleware("can:create, ".Teacher::class);

Route::post('/insert_teachers_organiki', [TeacherController::class, 'insertTeachersOrganiki']);

Route::view('/index_teacher', 'index_teacher'); // auth checking in view

Route::get('/teacher/{md5}', [TeacherController::class, 'login']);

Route::get('/tlogout', [TeacherController::class, 'logout']);

Route::get('/teacher_view/{form}',[TeacherController::class,'makeForm'])->middleware('can:view,form');

Route::get('/teacher_app/{appname}', function($appname){
    $microapp = Microapp::where('url', '/'.$appname)->first(); //there is one result because if there wasn't the middleware would throw 404
    if($microapp->active){
        return view('microapps.teacher.'.$appname,['appname'=>$appname]);
    }
    else{
        return redirect(url('/index_teacher'))->with('warning', "Η εφαρμογή $microapp->name είναι ανενεργή");
    }
})->middleware('canViewMicroapp');//will throw a 404 if the url does not exist or a 403 if teacher is not in the stakeholders of this microapp

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
    return view('microapp-profile',['microapp'=>$microapp]);
})->middleware('can:beViewedByAdmins,microapp')->middleware('can:update,microapp' );

Route::post('/save_microapp/{microapp}', [MicroappController::class,'saveProfile']);

Route::post("/change_microapp_status/{microapp}",[MicroappController::class, 'changeMicroappStatus']);

Route::post("/microapp_onoff/{microapp}",[MicroappController::class, 'onOff']);

// FILESHARES ROUTES

Route::get("/teacher_fileshare/{teacher}", function(Teacher $teacher){
    if(Auth::guard('teacher')->id()<>$teacher->id){
        abort(403);
    }
    return view('teacher-fileshare', ['teacher' => $teacher]);
});

Route::get("/school_fileshare/{school}", function(School $school){
    if(Auth::guard('school')->id()<>$school->id){
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

// Route::get('storage/app/{filename}', function ($filename) {
//     $filePath = storage_path('app/'.$filename);
//     // dd(basename($filePath));
//     if (file_exists($filePath)) {
//         return response()->file($filePath);
//     }

//     abort(403);
// })->where('filename','.*')->middleware('download');



//// TESTING ////////
// Route::get('/test/{teacher}', [TeacherController::class, 'test']);

// Route::view('/test','test');

// Route::get('/skata_test', [MicroappController::class, 'test']);

// Route::get('/manage_test', function(){
//     return view('welcome');
// });

// Route::post("/save_all_day/{school}", [AllDayController::class, 'saveData']);

//ADMIN Routes

Route::view('/', 'index')->name('index');

Route::get('/admin/{appname}', function($appname){
    $microapp = Microapp::where('url', '/'.$appname)->first(); //there is one result because if there wasn't the middleware would throw 404
    if($microapp->active){
        return view('microapps.admin.'.$appname,['appname'=>$appname]);
    }
    else{
        return redirect(url('/index'))->with('warning', "Η εφαρμογή $microapp->name είναι ανενεργή");
    }
})->middleware('canViewMicroapp');//will throw a 404 if the url does not exist or a 403 if teacher is not in the stakeholders of this microapp



// WHOCAN Routes
Route::get('/import_whocan/{my_app}/{my_id}', function($my_app, $my_id){
    return view('import-whocan', ['my_app'=>$my_app, 'my_id'=>$my_id]);
});

Route::post('/upload_whocan/{my_app}/{my_id}', [WhocanController::class, 'importStakeholdersWhoCan']);

Route::post('/insert_whocan/{my_app}/{my_id}', [WhocanController::class, 'insertWhocans']);

Route::post("/delete_all_whocans/{my_app}/{my_id}", [WhocanController::class, 'delete_all_whocans']);

Route::post("/delete_one_whocan/{my_app}/{my_id}", [WhocanController::class, 'delete_one_whocan']);