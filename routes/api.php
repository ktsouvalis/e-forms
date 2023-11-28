<?php

use App\Models\Fileshare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileshareController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('/insert_fileshare', function(){
//     $table = [
//         'name' => 'api_fileshare',
//         'department_id'=>1
//     ];

//     $fileshareHandler = New FileshareController();
//     $result = $fileshareHandler->create_fileshare($table);
//     if($result->getStatusCode() == 200){
//         $fileshare = Fileshare::find($result->getData()->fileshare);
//         $department_name = $fileshare->department->name;
//         Log::channel('user_memorable_actions')->info("api user create_fileshare ".$fileshare->name." for ".$department_name);
//     }
//     else{
//         Log::channel('throwable_db')->info("api user create_fileshare failed");
//     }

//     return response()->json($result->getData(), $result->getStatusCode());
// });
