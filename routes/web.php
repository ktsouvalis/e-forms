<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/', function () {
    return view('index');
})->name('index');

Route::post('/login', [UserController::class,'login'])->middleware('guest');

Route::get('/logout',[UserController::class, 'logout'])->middleware('auth');

Route::get('/password_change', function(){
    return view('password_change_form');
})->middleware('auth');

Route::post('/password_change', [UserController::class, 'passwordChange'])->middleware('auth');

Route::get('/manage_users', function(){
    return view('users');
});

Route::post('/user_template_upload', [UserController::class, 'importUsers'])->name('user_template_upload');

Route::post('/users_insertion', [UserController::class, 'insertUsers'])->name('insert_users_from_template');

Route::post('/manage_users/{action}', [UserController::class,'actions']);

Route::post('/password_reset', [UserController::class, 'passwordReset']);
