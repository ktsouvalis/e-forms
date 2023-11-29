<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    //

    public function upload_file($directory, $file, $driver){
        $filename = $file->getClientOriginalName();
        try{
            Storage::disk($driver)->putFileAs($directory, $file, $filename);
        }
        catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 500);
        }
        return response()->json(['success'=>'File uploaded successfully'], 200);
    }

    public function download_file($directory, $original_filename, $driver){
        try{
            ob_end_clean();
            return Storage::disk($driver)->download($directory."/".$original_filename);   
        }
        catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 500);
        }
    }

    public function delete_file($directory, $original_filename, $driver){
        try{
            Storage::disk($driver)->delete($directory."/".$original_filename);
        }
        catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 500);
        }
        return response()->json(['success'=>'File deleted successfully'], 200);   
    }

    public function delete_directory($directory, $driver){
        try{
            Storage::disk($driver)->deleteDirectory($directory);
        }
        catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 500);
        }
        return response()->json(['success'=>'Directory deleted successfully'], 200);   
    }
}
