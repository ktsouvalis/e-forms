<?php

namespace App\Http\Controllers;

use Exception;
use ZipArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class FilesController extends Controller
{
    //

    public function upload_file($directory, $file, $driver, $desiredFilename = null){
        $filename = $file->getClientOriginalName();
        if($desiredFilename){
            if(strpos(substr($desiredFilename, -6), ".")){//if there is an extension to given filename
                $filename = $desiredFilename;
            } else {//find the extension and add it to the given filename
                $extension = $file->extension();
                $filename = $desiredFilename.$extension;
            }
        }
        try{
            Storage::disk($driver)->putFileAs($directory, $file, $filename);
        }
        catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 500);
        }
        return response()->json(['success'=>'File uploaded successfully'], 200);
    }

    public function download_file($directory, $original_filename, $driver, $desiredFilename = null){
        if($desiredFilename){
            $extension = pathinfo($desiredFilename, PATHINFO_EXTENSION);
            if($extension){//if there is an extension to given filename
                $filename = $desiredFilename;
            } else {//find the extension and add it to the given filename
                $extension = pathinfo($original_filename, PATHINFO_EXTENSION);
                $filename = $desiredFilename.$extension;
            }
        } else {
            $filename = $original_filename;
        }
        try{
            ob_end_clean();
            return Storage::disk($driver)->download($directory."/".$original_filename, $filename);
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

    public function download_directory_as_zip($directory){
        set_time_limit(0);//maximum execution of the script unlimited
        $tempZipFile = tempnam(sys_get_temp_dir(), 'dir_zip_');
        $zip = new ZipArchive();
        if ($zip->open($tempZipFile, ZipArchive::CREATE) !== true) {
            abort(500, 'Failed to create zip archive');
        }

        ini_set('max_execution_time', 0);//maximum execution time to php configuration unlimited (for large archives)
        $files = Storage::allFiles($directory);
        foreach ($files as $file) {
            $relativePath = str_replace($directory . '/', '', $file); // Remove the directory prefix
            $zip->addFile(Storage::path($file), $relativePath);
        }
        $zip->close();

        ini_restore('max_execution_time');
        $zipFileName = $directory === '/' ? 'root_directory' : basename($directory);

        $headers = [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $zipFileName . '.zip"',
        ];

        ob_end_clean();
        try {
            return Response::download($tempZipFile, $zipFileName . '.zip', $headers)->deleteFileAfterSend(true);
        } 
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }     
}
