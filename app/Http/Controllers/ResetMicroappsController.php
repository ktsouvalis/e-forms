<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Microapp;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ResetMicroappsController extends Controller
{
    //
    public function reset_microapp(Request $request, Microapp $microapp){
        return view("reset_microapp", ['microapp' => $microapp]);
    }

    public function download_files(Request $request, Microapp $microapp){
        $directory = str_replace("/", "", $microapp->url);
        $directoryHandler = new FilesController;
        $files = $directoryHandler->download_directory_as_zip($directory);

        if($files->getStatusCode()=='500'){
            Log::channel('files')->error(Auth::user()->username." failed to download $directory files: ".json_decode($files->getContent(),true)['error']);
            return back()->with('failure', json_decode($files->getContent(),true)['error'].'. Επικοινωνήστε με τον διαχειριστή. ');
        }

        Log::channel('files')->info(Auth::user()->username." successfully downloaded $directory files");
        if (ob_get_length()) {
            ob_end_clean();
        }
        return $files;
    }

    public function download_excel(Request $request, Microapp $microapp)
    {
        try {
            // Start output buffering to catch accidental output
            ob_clean(); // clear any existing output
            ob_start();
            
            $model_name = $this->get_modelname($microapp);
            $modelClass = "App\\Models\\microapps\\" . $model_name;

            if (!class_exists($modelClass)) {
                return response()->json(['error' => "Model $model_name not found."], 404);
            }

            $data = $modelClass::all();
            
            if ($data->isEmpty()) {
                return response()->json(['error' => 'No data found in the table.'], 404);
            }

            $columns = Schema::getColumnListing((new $modelClass)->getTable());

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Add header row
            foreach ($columns as $colIndex => $column) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, 1, $column);
            }

            // Add data rows
            foreach ($data as $rowIndex => $row) {
                $rowArray = $row->toArray();
                foreach ($columns as $colIndex => $column) {
                    $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 2, $rowArray[$column] ?? '');
                }
            }

            // Send headers and file
            $filename = 'export_' . $model_name . '_' . date('Y-m-d_H-i-s') . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');

            // Clean output buffer and terminate
            ob_end_flush();
            exit;

        } catch (\Throwable $e) {
            // Log the error and return JSON instead of corrupting Excel output
            Log::error('Excel export failed: ' . $e->getMessage(). json_encode($rowArray));
            return response()->json(['error' => 'Failed to export Excel.'], 500);
        }
    }


    public function delete_files(Request $request, Microapp $microapp)
    {
        //$this->authorize('edit', $microapp);/*****CHECK this IF is needed */
        $username = Auth::check() ? Auth::user()->username : "API";
        $error=false;
        //delete files from disk
        $directory = str_replace("/", "", $microapp->url);
        $directoryHandler = new FilesController;

        $delete_directory = $directoryHandler->delete_directory($directory, 'local');
        if($delete_directory->getStatusCode() == 500){
            Log::channel('files')->error($username." ResetMicroapp's directory $directory failed to delete");
            $error=true;
        }
        else
            Log::channel('files')->info($username." ResetMicroapp's directory $directory deleted successfully");
        Log::channel('user_memorable_actions')->info($username." delete_files ".$microapp->url);
        if(!$error){
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }
            return redirect(url($microapp->url))->with('success', "Τα αρχεία μέσω του ResetMicroapp $microapp->url διαγράφηκαν");
        }
        else{
            return redirect(url($microapp->url))->with('warning', "Τα αρχεία μέσω του ResetMicroapp $microapp->url διαγράφηκαν με σφάλματα (files)");
        }
    }

    public function reset_db(Request $request, Microapp $microapp)
    {
        //$this->authorize('view', $microapp);/*****CHECK this IF is needed */
        $username = Auth::check() ? Auth::user()->username : "API";            
        $dbtable_name = str_replace("/", "", $microapp->url);
        $model_name = ucfirst(rtrim($dbtable_name, 's'));
        $modelClass = "App\\Models\\microapps\\" . $model_name;

        if (!class_exists($modelClass)) {
            return response()->json(['error' => "Model $model_name not found."], 404);
        }
        // delete database record
        try{
            $modelClass::truncate();
            
            return redirect(url($microapp->url))->with('success', "Όλα τα δεδομένα του πίνακα {$model_name} διαγράφηκαν.");
        }
        catch(\Exception $e){
            Log::channel('throwable_db')->error($username."failed to truncate {$model_name}: " . $e->getMessage());
            
             return redirect(url($microapp->url))->with('failure', "Ο πίνακας {$model_name} δεν διαγράφηκε (throwable_db)");
        }
    }

    private function get_modelname(Microapp $microapp){
        
        $input = ltrim($microapp->url, '/');

        // Split by underscore
        $parts = explode('_', $input);

        // Capitalize each part
        $parts = array_map('ucfirst', $parts);

        // Join into one string
        $model_name = implode('', $parts);
        
        if (str_ends_with($model_name, 's')) {
            $model_name = substr($model_name, 0, -1);
        }

        return $model_name;
    }

}
