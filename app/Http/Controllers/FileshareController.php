<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use App\Models\Fileshare;
use Illuminate\Http\Request;
use App\Models\FileshareDepartment;
use App\Models\FileshareStakeholder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

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
        $directory_common = 'public/file_shares/fileshare'.$newFileshare->id;
        $directory_personal = $directory_common.'/personal_files';
        $common_files = $request->file('fileshare_common_files');
        if ($common_files) {
            foreach ($common_files as $file) {
                $path = $file->storeAs($directory_common, $file->getClientOriginalName());
            }
        }

        $personal_files = $request->file('fileshare_personal_files');
        if ($personal_files) {
            foreach ($personal_files as $file) {
                $path = $file->storeAs($directory_personal, $file->getClientOriginalName());
            }
        }

        return redirect(url('/fileshares'))->with('success', 'Ο διαμοιρασμός αρχείων δημιουργήθηκε. Μπορείτε να προσθέσετε και άλλα αρχεία στη συνέχεια.');
    }

    public function update_fileshare(Request $request, Fileshare $fileshare){

        $edited=false;
        
        //update name
        $fileshare->name = $request->all()['name'];
        if($fileshare->isDirty('name')){
            $fileshare->save();
            $edited=true;
        }

        //update or add files
        
        $directory_common = 'public/file_shares/fileshare'.$fileshare->id;
        $directory_personal = $directory_common.'/personal_files';
        
        $common_files = $request->file('fileshare_common_files');
        if ($common_files) {
            foreach ($common_files as $file) {
                $path = $file->storeAs($directory_common, $file->getClientOriginalName());
            }
        }

        $personal_files = $request->file('fileshare_personal_files');
        if ($personal_files) {
            foreach ($personal_files as $file) {
                $path = $file->storeAs($directory_personal, $file->getClientOriginalName());
            }
        }

        if($edited){
            return redirect(url("/fileshare_profile/$fileshare->id"))->with('success', 'Προστέθηκαν τα αρχεία που ανεβάσατε.');
        }
        else{
            return redirect(url("/fileshare_profile/$fileshare->id"))->with('warning', 'Δεν υπάρχουν αλλαγές προς αποθήκευση');   
        }
    }

    // public function getGlobalFilesToShow($id){
    // $globalPath = dirname(dirname(dirname(__DIR__)))."/storage/app/file_shares/fileshares".$id;
    //    $filecounter = 0;
    //     $globalFilesToShow = array();
    //     $scandir = scandir($globalPath);
    //     foreach($scandir as $file){
    //         if($file!='.' && $file!='..' && $file!= 'personal'){
    //             $filesToShow[$filecounter] = $globalPath."/".$file;
    //             $filecounter++;
    //         }
    //     }
    //     return $filesToShow;
    // }
    // public function getPersonalFilesToShow($id, $afm){
    //     $globalPath = "files/fileshare".$id;
    //     $personalPath=$globalPath."/personal";
    //     $filecounter=0;
    //     $personalFilesToShow = array();
    //     $scanPersdir = scandir($personalPath);
    //     foreach($scanPersdir as $file){
    //         if($file!='.' && $file!='..' && $this->persFilesExist($file, $afm)){
    //         $personalFilesToShow[$filecounter] = "$personalPath/".$file;
    //                 $filecounter++;
    //             }
    //         }
    //         return $personalFilesToShow;
    //     }

    // private function persFilesExist($filename, $afm){
    //     for($i=0;$i<strlen($filename);$i++){
    //         if(substr($filename, $i, 9) == $afm){
    //             return 1;
    //         }
    //     }
    //     return 0;
    // }
}
