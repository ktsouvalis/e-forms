<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    //

    public function upload_file($directory, $file, $driver){
        $filename = $file->getClientOriginalName();
        try{
            $file->storeAs($directory, $filename, $driver);
        }
        catch(Exception $e){
            return response()->json(['error'=>$e->getMessage()], 500);
        }
        return response()->json(['success'=>'File uploaded successfully'], 200);
    }
}
