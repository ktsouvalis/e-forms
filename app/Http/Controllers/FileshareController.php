<?php

namespace App\Http\Controllers;

use App\Models\Fileshare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FileshareDepartment;

class FileshareController extends Controller
{
    //
    public function insert_fileshare(Request $request){
       
        $newFileshare = Fileshare::create([
            'name' => $request->all()['fileshare_name'],
            'department_id' => Auth::user()->department->id
        ]);
        
        
        //stefanopoulos version
        // $directory_common = 'file_shares/fileshares'.$newFileshare->id.'/common_files';
        // $directory_personal = 'file_shares/fileshares'.$newFileshare->id.'/personal_files';
       
        //tsouvalis version
        $directory_common = 'file_shares/fileshares'.$newFileshare->id;
        $directory_personal = $directory_common.'/personal_files';
        
        //dd($request);
        $common_files = $request->file('fileshare_common_files');
        if ($common_files) {
            foreach ($common_files as $file) {
                $path = $file->storeAs($directory_common, $file->getClientOriginalName());
                echo $path;
            }
        }

        $personal_files = $request->file('fileshare_personal_files');
        if ($personal_files) {
            foreach ($personal_files as $file) {
                $path = $file->storeAs($directory_personal, $file->getClientOriginalName());
                echo $path;
            }
        }
    }
}
