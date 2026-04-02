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
            if(strpos(substr($desiredFilename, -6), ".")){
                $filename = $desiredFilename;
            } else {
                $extension = $file->extension();
                $filename = $desiredFilename.$extension;
            }
        }

        // Auto-compress PDFs larger than 2MB
        $compressedPath = null;
        if($file->getClientMimeType() === 'application/pdf' && $file->getSize() > 2 * 1024 * 1024){
            try {
                $compressedPath = $this->compress_pdf($file);
                $fileToStore = new \Illuminate\Http\File($compressedPath);
            } catch(\Exception $e) {
                return response()->json(['error' => 'PDF compression failed: ' . $e->getMessage()], 500);
            }
        } else {
            $fileToStore = $file;
        }

        try{
            Storage::disk($driver)->putFileAs($directory, $fileToStore, $filename);
        }
        catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 500);
        }
        finally{
            // Always clean up the temp file if it was created
            if($compressedPath && file_exists($compressedPath)){
                unlink($compressedPath);
            }
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
            if(!Storage::disk($driver)->exists($directory."/".$original_filename)){
                return response()->json(['error'=>'File not found'], 404);
            }
            Storage::disk($driver)->delete($directory."/".$original_filename);
            return response()->json(['success'=>'File deleted successfully'], 200);
        }
        catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 500);
        }
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
    
    private function compress_pdf($file, $targetSizeKB = 2048)
    {
        $inputPath = $file->getRealPath();
        $outputPath = sys_get_temp_dir() . '/' . uniqid('compressed_', true) . '.pdf';

        // Ghostscript compression settings (screen = aggressive, ebook = balanced)
        $gsSettings = 'ebook';

        $command = sprintf(
            'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/%s ' .
            '-dNOPAUSE -dQUIET -dBATCH -sOutputFile=%s %s 2>&1',
            $gsSettings,
            escapeshellarg($outputPath),
            escapeshellarg($inputPath)
        );

        shell_exec($command);

        if (!file_exists($outputPath) || filesize($outputPath) === 0) {
            throw new Exception('PDF compression failed.');
        }

        return $outputPath; // returns path to the compressed temp file
    }
}
