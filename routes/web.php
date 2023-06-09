<?php

use App\Models\User;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Microapp;
use App\Models\Operation;
use App\Policies\FormPolicy;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\MicroappController;
use App\Http\Controllers\OperationController;

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

//// USER ///////////////////////////////////////

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

//////// SCHOOL ////////////////////////////////////////////////////////////

Route::view('/schools', 'schools');

Route::post('/upload_schools_template', [SchoolController::class, 'importSchools']);

Route::view('/import_schools', "import-schools")->middleware('can:upload, '.School::class);

Route::view('/preview_schools', "preview-schools");

Route::post('/insert_schools', [SchoolController::class, 'insertSchools']);

Route::get('/school/{md5}', [SchoolController::class, 'login']);

Route::view('/index_school', 'index_school');

Route::get('/slogout', [SchoolController::class, 'logout']);

Route::get('/school_app/all_day', function(){
    return view('welcome');
})->middleware('canViewMicroapp');

//////// TEACHER //////////////////////////////////////////////////////////

Route::view('/teachers','teachers');

Route::view('/import_teachers', 'import-teachers')->middleware("can:create, ".Teacher::class);

Route::post('/upload_teachers_organiki_template', [TeacherController::class, 'importTeachersOrganiki']);

Route::view('/preview_teachers_organiki', 'preview-teachers-organiki');

Route::post('/insert_teachers_organiki', [TeacherController::class, 'insertTeachersOrganiki']);

Route::view('/index_teacher', 'index_teacher');

Route::get('/teacher/{md5}', [TeacherController::class, 'login']);

Route::get('/tlogout', [TeacherController::class, 'logout']);

Route::get('/teacher_view/{form}',[TeacherController::class,'makeForm'])->middleware('can:view,form,' . FormPolicy::class);

//////// OPERATIONS ////////////////////////////////////////////////////

Route::view('/manage_operations', 'operations')->middleware('boss');

Route::get('/operation_profile/{operation}', function(Operation $operation){
    return view('operation-profile',['operation'=>$operation]);
})->middleware('boss');

Route::post('/save_operation/{operation}', [OperationController::class,'saveProfile']);

Route::post('/insert_operation', [OperationController::class,'insertOperation']);

// Route::post('/change_operation_status', [OperationController::class,'changeOperationStatus']);

//////// MICROAPPS ////////////////////

Route::view('/microapps', 'microapps');

Route::post('/insert_microapp', [MicroappController::class,'insertMicroapp']);

Route::get('/microapp_profile/{microapp}', function(Microapp $microapp){
    return view('microapp-profile',['microapp'=>$microapp]);
})->middleware('can:update,microapp' );

Route::post('/save_microapp/{microapp}', [MicroappController::class,'saveProfile']);

Route::get('/import_whocan/{microapp}', function(Microapp $microapp){
    return view('import-whocan',['microapp'=> $microapp]);
})->middleware('can:update,microapp' );

Route::post('/upload_whocan/{microapp}', [MicroappController::class, 'importStakeholdersWhoCan']);

Route::post('/insert_whocan/{microapp}', [MicroappController::class, 'insertWhocans']);

Route::post("/change_microapp_status/{microapp}",[MicroappController::class, 'changeMicroappStatus']);

//// TESTING ////////
// Route::get('/test/{teacher}', [TeacherController::class, 'test']);

Route::view('/test','test');

Route::get('/skata_test', [MicroappController::class, 'test']);

Route::get('/manage_test', function(){
    return view('welcome');
});