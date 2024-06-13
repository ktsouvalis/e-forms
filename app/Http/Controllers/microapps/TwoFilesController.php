<?php

namespace App\Http\Controllers\microapps;

use App\Models\Microapp;
use Illuminate\Http\Request;
use App\Models\microapps\TwoFile;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Validator;

class TwoFilesController extends Controller
{
    //
    public function __construct(){
        $this->middleware('auth')->only(['index', 'export_xlsx']);
        $this->middleware('isSchool')->only(['create']);
        $this->microapp = Microapp::where('url', '/two_files')->first();
    }

    public function index(){
        return view('microapps.two_files.index');
    }

    public function create(){
        //dd('create');
        return view('microapps.two_files.create', ['school' => Auth::guard('school')->user()]);
    }

    public function upload_file(Request $request, $upload_file_name){//app_use
        $school = Auth::guard('school')->user();
        $error=false;
        $directory = 'two_files';
        if(null !== $request->file('file')){
            $file = $request->file('file');
            $rule = [
                'file' => 'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];
            //ανέβασε το αρχείο και επέστρεψε
            $fileHandler = new FilesController();
            $upload  = $fileHandler->upload_file($directory, $file, 'local',  $upload_file_name);
            if($upload->getStatusCode() == 500){
                return back()->with('failure', 'Δοκιμάστε ξανά');
            } else {
                return back()->with('success', 'Το αρχείο ανέβηκε επιτυχώς');
            }
        }
        if(null !== $request->file('fileXlsx')){
            $file = $request->file('fileXlsx');
            $rule = [
                'fileXlsx' => 'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];
            $filename_to_store = "TwoFiles_".$school->code.".xlsx";
            $values = array(
                'fileXlsx' => $request->file('fileXlsx')->getClientOriginalName(),
            );
        }
        if(null !== $request->file('filePdf')){
            $file = $request->file('filePdf');
            $rule = [
                'filePdf' => 'mimetypes:application/pdf'
            ];
            $filename_to_store = "TwoFiles_".$school->code.".pdf";
            $values = array(
                'filePdf' => $request->file('filePdf')->getClientOriginalName(),
            );
        }
        //validate file
        $validator = Validator::make($request->all(), $rule);

        if($validator->fails()){
            return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
        }
        // store  file
        $fileHandler = new FilesController();
        $upload  = $fileHandler->upload_file($directory, $file, 'local',  $filename_to_store);
        if($upload->getStatusCode() == 500){
            $error=true;
        }
        try{
            $twoFile = TwoFile::updateOrCreate([
                'school_id' => $school->id
            ], $values);
        } catch(\Exception $e){
            dd($e->getMessage());
            $error=true;
        }
        if(!$error){
            
            return back()->with('success', 'Το αρχείο ανέβηκε επιτυχώς');
        }
            
        else
            
            return back()->with('failure', 'Προσπαθήστε ξανά');
    }

    public function download_file($file, $download_file_name = null){
        
        $username = Auth::check() ? Auth::user()->username : (Auth::guard('school')->check() ? Auth::guard('school')->user()->name : Auth::guard('teacher')->user()->afm);
        $directory = "two_files";
        $fileHandler = new FilesController();
        $download = $fileHandler->download_file($directory, $file, 'local', $download_file_name);
        if($download->getStatusCode() == 500){
            // dd($download->getContent());
            Log::channel('files')->error($username." File $file failed to download");
            return back()->with('failure', 'Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($username." File $file successfully downloaded");
        return $download;
    }

    

}
