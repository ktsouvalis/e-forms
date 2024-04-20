<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EvaluationController extends Controller
{
    //
    public function upload_csv(Request $request)
    {   
        $directory = 'evaluation';
        $fileHandler = new FilesController();
    try {
        switch ($request->action) {
        case 'a1':
            if($request->hasFile('a1')){
                DB::table('evaluation_a1')->truncate();
                $file = $request->file('a1');
                $file_name = $file->getClientOriginalName();
                $upload  = $fileHandler->upload_file($directory, $file, 'local');
                $filename = storage_path('app/evaluation').'/'.$file_name;
                $handle = fopen($filename, 'r');
                stream_filter_append($handle, 'convert.iconv.UTF-8/UTF-8//IGNORE');
                fgetcsv($handle, 1000, ",");
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Process each row of data here
                    $teacher = Teacher::where('afm', $data[2])->first();
                    DB::table('evaluation_a1')->insert([
                        'teacher_afm' => $data[2],
                        'evaluator_afm' => $data[6],
                        'date_in' => Carbon::today(),
                    ]);
                    if($data[3]){
                        $date = Carbon::createFromFormat('d/m/Y H:i:sO', $data[3]);   
                        $formattedDate = $date->format('Y-m-d H:i:s');
                        $line = DB::table('evaluation_a1')->where('teacher_afm', $data[2])->update(['self_evaluation_date' => $formattedDate]);
                    }
                    $evaluator = Teacher::where('afm', $data[6])->first();
                    if(!$evaluator){
                        $ev_name_surname = $data[5]." ".$data[4];
                        DB::table('evaluation_a1')->where('teacher_afm', $data[2])->update(['evaluator_afm_comments' => $ev_name_surname]);
                    }
                    if($data[7]){
                        $date = Carbon::createFromFormat('d/m/Y H:i:sO', $data[7]);   
                        $formattedDate = $date->format('Y-m-d H:i:s');
                        $line = DB::table('evaluation_a1')->where('teacher_afm', $data[2])->update(['date_completed_timestamp' => $formattedDate]);
                    }
                }

                fclose($handle);
            }
            break;
        case 'a2':
            if($request->hasFile('a2')){
                DB::table('evaluation_a2')->truncate();
                $file = $request->file('a2');
                $file_name = $file->getClientOriginalName();
                $upload  = $fileHandler->upload_file($directory, $file, 'local');
                $filename = storage_path('app/evaluation').'/'.$file_name;
                $handle = fopen($filename, 'r');
                stream_filter_append($handle, 'convert.iconv.UTF-8/UTF-8//IGNORE');
                fgetcsv($handle, 1000, ",");
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    
                    // Process each row of data here
                    $teacher = Teacher::where('afm', $data[2])->first();
                    DB::table('evaluation_a2')->insert([
                        'teacher_afm' => $data[2],
                        'evaluator_afm' => $data[6],
                        'date_in' => Carbon::today(),
                    ]);
                    if($data[3]){
                        $date = Carbon::createFromFormat('d/m/Y H:i:sO', $data[3]);   
                        $formattedDate = $date->format('Y-m-d H:i:s');
                        $line = DB::table('evaluation_a2')->where('teacher_afm', $data[2])->update(['self_evaluation_date' => $formattedDate]);
                    }
                    $evaluator = Teacher::where('afm', $data[6])->first();
                    if(!$evaluator){
                        $ev_name_surname = $data[5]." ".$data[4];
                        DB::table('evaluation_a1')->where('teacher_afm', $data[2])->update(['evaluator_afm_comments' => $ev_name_surname]);
                    }
                    if($data[7]){
                        $date = Carbon::createFromFormat('d/m/Y H:i:sO', $data[7]);   
                        $formattedDate = $date->format('Y-m-d H:i:s');
                        $line = DB::table('evaluation_a2')->where('teacher_afm', $data[2])->update(['date_completed_timestamp' => $formattedDate]);
                    }
                }
                fclose($handle);
            }
            break;
        case 'b':
            if($request->hasFile('b')){
                DB::table('evaluation_b')->truncate();
                $file = $request->file('b');
                $file_name = $file->getClientOriginalName();
                $upload  = $fileHandler->upload_file($directory, $file, 'local');
                $filename = storage_path('app/evaluation').'/'.$file_name;
                $handle = fopen($filename, 'r');
                stream_filter_append($handle, 'convert.iconv.UTF-8/UTF-8//IGNORE');
                fgetcsv($handle, 1000, ",");
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Process each row of data here
                    $teacher = Teacher::where('afm', $data[2])->first();
                    DB::table('evaluation_b')->insert([
                        'teacher_afm' => $data[2],
                        'evaluator_1_afm' => $data[6],
                        'evaluator_2_afm' => $data[9],
                        'date_in' => Carbon::today(),
                    ]);
                    if($data[3]){
                        $date = Carbon::createFromFormat('d/m/Y H:i:sO', $data[3]);   
                        $formattedDate = $date->format('Y-m-d H:i:s');
                        $line = DB::table('evaluation_b')->where('teacher_afm', $data[2])->update(['self_evaluation_date' => $formattedDate]);
                    }
                    if($data[10]){
                        $date = Carbon::createFromFormat('d/m/Y H:i:sO', $data[10]);   
                        $formattedDate = $date->format('Y-m-d H:i:s');
                        $line = DB::table('evaluation_b')->where('teacher_afm', $data[2])->update(['date_completed_timestamp' => $formattedDate]);
                    }
                }
                fclose($handle);
            }
            break;
        }
    } catch (\Exception $e) {
        dd($e);
        return back()->with('error', 'File upload failed');
    }
        return back()->with('success', 'File uploaded successfully');
    }
}
