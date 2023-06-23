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
        
        $directory_common = 'fileshare'.$newFileshare->id;
        $directory_personal = $directory_common.'/personal_files';
        $common_files = $request->file('fileshare_common_files');
        if ($common_files) {
            foreach ($common_files as $file) {
                $path = $file->storeAs($directory_common, $file->getClientOriginalName(), 'local');
            }
        }

        $personal_files = $request->file('fileshare_personal_files');
        if ($personal_files) {
            foreach ($personal_files as $file) {
                $path = $file->storeAs($directory_personal, $file->getClientOriginalName(), 'local');
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
        
        $directory_common = 'fileshare'.$fileshare->id;
        $directory_personal = $directory_common.'/personal_files';
        $common_files = $request->file('fileshare_common_files');
        if ($common_files) {
            foreach ($common_files as $file) {
                $path = $file->storeAs($directory_common, $file->getClientOriginalName(), 'local');
                // $path = $file->storeAs($directory_common, $file->getClientOriginalName(), 'public');
            }
            $edited=true;
        }

        $personal_files = $request->file('fileshare_personal_files');
        if ($personal_files) {
            foreach ($personal_files as $file) {
                $path = $file->storeAs($directory_personal, $file->getClientOriginalName(), 'local');
                // $path = $file->storeAs($directory_personal, $file->getClientOriginalName(), 'public');
            }
        }

        if($edited){
            return redirect(url("/fileshare_profile/$fileshare->id"))->with('success', 'Προστέθηκαν τα αρχεία που ανεβάσατε.');
        }
        else{
            return redirect(url("/fileshare_profile/$fileshare->id"))->with('warning', 'Δεν υπάρχουν αλλαγές προς αποθήκευση');   
        }
    }

    public function delete_fileshare(Request $request, Fileshare $fileshare){
        Fileshare::destroy($fileshare->id);
        Storage::disk('local')->deleteDirectory('fileshare'.$fileshare->id);

        return redirect(url('/fileshares'))->with('success', "Η κοινοποίηση αρχείων $fileshare->name διαγράφηκε");
    }

    public function download_file(Request $request, Fileshare $fileshare){
        $file = $request->input('filename');
        return Storage::disk('local')->download($file);
    }

    public function delete_file(Request $request, Fileshare $fileshare){
        $file = $request->input('filename');
        Storage::disk('local')->delete($file);
        $fn = basename($file);
        
        return back()->with('success', "Το αρχείο $fn αφαιρέθηκε από τον διαμοιρασμό");
    }
}
