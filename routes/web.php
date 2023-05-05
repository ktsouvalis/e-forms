<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
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

Route::post('/upload_user_template', [UserController::class, 'importUsers'])->name('user_template_upload')->middleware('boss');

Route::view('/manage_users', 'users')->middleware('boss');

Route::post('/insert_users', [UserController::class, 'insertUsers'])->name('insert_users_from_template')->middleware('boss');

Route::post('/insert_user', [UserController::class,'insertUser'])->middleware('boss');

Route::post('/reset_password/{user}', [UserController::class, 'passwordReset'])->middleware('auth');

Route::view('/manage_menus', 'menus')->middleware('boss');

Route::post('/insert_menu', [MenuController::class,'insertMenu'])->middleware('boss');
