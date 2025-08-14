<?php

namespace App\Http\Controllers;

use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\FilesController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            // Remove all output buffering completely
            while (ob_get_level()) {
                ob_end_clean();
            }

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
            //dd($data, $columns);
            // Generate filename
            $filename = 'export_' . $model_name . '_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Create writer first to catch any potential errors
            $writer = new Xlsx($spreadsheet);
            // Save the file to storage/app/exports directory
            //savePath = storage_path($filename);
            //dd($savePath, $writer);
            //$writer->save($savePath);
            //dd($filename, $writer);
            return response()->streamDownload(
                function () use ($writer) {
                    $writer->save('php://output');
                },
                $filename,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                    'Pragma' => 'public'
                ]
            );

        } catch (\Throwable $e) {
            Log::error('Excel export failed: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(['error' => 'Failed to export Excel. Please try again.'], 500);
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
        $microapp_name = str_replace("/", "", $microapp->url);

        $tablesToTruncate = $this->getTablesToTruncateFromModelName($microapp_name);
        if(!$tablesToTruncate){
            Log::channel('throwable_db')->error($username." failed to get tables to truncate for microapp {$microapp->url}");
            return redirect(url($microapp->url))->with('failure', "Δεν βρέθηκαν πίνακες για διαγραφή δεδομένων (throwable_db)");
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        foreach ($tablesToTruncate as $table) {
            try{
                DB::table($table)->truncate();
            } catch(\Exception $e){
                Log::channel('throwable_db')->error($username." failed to truncate table {$table}: " . $e->getMessage());
                return redirect(url($microapp->url))->with('failure', "Ο πίνακας {$table} δεν διαγράφηκε (throwable_db)");
            }
            Log::channel('throwable_db')->info($username." successfully truncated table {$table}");
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
        return redirect(url($microapp->url))->with('success', "Όλα τα δεδομένα της μικροεφαρμογής {$microapp->name} διαγράφηκαν.");
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

    private function getTablesToTruncateFromModelName($model_name)
    {
        dd($model_name);
        switch ($model_name) {
            
            case 'All_day_school':
                return ['all_day_school'];
            break;
            case 'outings':
                return ['outings_sections', 'outings'];
            break;
            case 'work_planning':
                return ['work_plans'];
            break;
            default:
                // Assuming the model name corresponds to a table with the same name
                $tableName = strtolower($model_name);
                if (Schema::hasTable($tableName)) {
                    return [$tableName];
                } else {
                    Log::channel('throwable_db')->error("Table {$tableName} does not exist for model {$model_name}");
                    return null; // or throw an exception
                }
            break;
            // Add more cases for other models if needed
        }
    }
        

}
