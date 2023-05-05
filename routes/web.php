<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;

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

Route::view('/', 'index')->name('index');

Route::post('/login', [UserController::class,'login'])->middleware('guest');

Route::get('/logout',[UserController::class, 'logout'])->middleware('auth');

Route::view('/change_password', 'password_change_form')->middleware('auth');

Route::post('/change_password', [UserController::class, 'passwordChange'])->middleware('auth');

Route::view('/manage_users', 'users')->name('manage_users')->middleware('hasAccess');

Route::post('/upload_user_template', [UserController::class, 'importUsers'])->name('upload_user_template');

Route::post('/insert_users', [UserController::class, 'insertUsers'])->name('insert_users');

Route::post('/insert_user', [UserController::class,'insertUser'])->name('insert_user');

Route::post('/reset_password/{user}', [UserController::class, 'passwordReset'])->middleware('auth');



Route::view('/manage_menus', 'menus')->name('manage_menus')->middleware('hasAccess');

Route::post('/insert_menu', [MenuController::class,'insertMenu'])->name('insert_menu');

Route::get('/manage_test', function(){
    return view('welcome');
})->name('manage_test')->middleware('hasAccess');