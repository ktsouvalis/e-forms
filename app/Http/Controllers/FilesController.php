<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    //

    public function upload_file($directory, $file, $driver, $desiredFilename = null){
        $filename = $file->getClientOriginalName();
        
        if($desiredFilename){
            if(strpos(substr($desiredFilename, -5), ".")){//if there is an extension to given filename
                $filename = $desiredFilename;
            } else {//find the extension and add it to the given filename
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $filename = $desiredFilename.$extension;
            }
            dd($filename);
        }
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
