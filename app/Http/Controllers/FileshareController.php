<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use App\Models\Fileshare;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\FileshareDepartment;
use Illuminate\Support\Facades\Log;
use App\Models\FileshareStakeholder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class FileshareController extends Controller
{
    /**
     * Insert a new fileshare into database.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function insert_fileshare(Request $request)
    {
        if($request->user()->can('chooseDepartment', Fileshare::class)){
            $department_id = $request->input('department');
        }
        else{
            $department_id = $request->user()->department->id;
        }

        $department_name = Department::find($department_id)->name;
        // create a database record
        try{
            $newFileshare = Fileshare::create([
                'name' => $request->all()['fileshare_name'],
                'department_id' => $department_id
            ]);   
        }
        catch(Throwable $e){
            Log::channel('throwable_db')->error(Auth::user()->username." insert_fileshare");
            return redirect(url('/fileshares'))->with('failure', $e);
        }

        Log::channel('user_memorable_actions')->info(Auth::user()->username." insert_fileshare ".$request->all()['fileshare_name']." for ".$department_name);
        return redirect(url('/fileshares'))->with('success', 'Ο διαμοιρασμός αρχείων δημιουργήθηκε. Μπορείτε να προσθέσετε αρχεία, ενδιαφερόμενους στη συνέχεια.');
    }

    /**
     * Update an existing fileshare.
     *
     * @param  Request  $request
     * @param  Fileshare  $fileshare
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_fileshare(Request $request, Fileshare $fileshare)
    {
        $old_name = $fileshare->name;
        // Update name
        $fileshare->name = $request->all()['name'];
        if ($fileshare->isDirty('name')) {
            Log::channel('user_memorable_actions')->info(Auth::user()->username." update_fileshare (rename) $old_name to ".$fileshare->name);
            $fileshare->save();
        }

        // set the directories for common and personal files
        $directory_common = 'fileshare'.$fileshare->id;
        $directory_personal = $directory_common.'/personal_files';

        $common_files = $request->file('fileshare_common_files');

        // store common files if any
        if ($common_files) {
            foreach ($common_files as $file) {
                $path = $file->storeAs($directory_common, $file->getClientOriginalName(), 'local');
            }
            Log::channel('user_memorable_actions')->info(Auth::user()->username." update_fileshare (added common files) ".$fileshare->name);
        }

        $personal_files = $request->file('fileshare_personal_files');

        // store personal files if any
        if ($personal_files) {
            foreach ($personal_files as $file) {
                $path = $file->storeAs($directory_personal, $file->getClientOriginalName(), 'local');
            }
            Log::channel('user_memorable_actions')->info(Auth::user()->username." update_fileshare (add personal files) ".$fileshare->name);
        }

        return redirect(url("/fileshare_profile/$fileshare->id"))->with('success', 'Αποθηκεύτηκε');
    }

    /**
     * Delete a whole fileshare.
     *
     * @param  Request  $request
     * @param  Fileshare  $fileshare
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete_fileshare(Request $request, Fileshare $fileshare)
    {
        //delete files from disk
        Storage::disk('local')->deleteDirectory('fileshare'.$fileshare->id);

        Log::channel('user_memorable_actions')->info(Auth::user()->username." delete_fileshare ".$fileshare->name);
        
        // delete database record
        Fileshare::destroy($fileshare->id);

        return redirect(url('/fileshares'))->with('success', "Η κοινοποίηση αρχείων $fileshare->name διαγράφηκε");
    }

    /**
     * Download a file from the fileshare.
     *
     * @param  Request  $request
     * @param  Fileshare  $fileshare
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download_file(Request $request, Fileshare $fileshare)
    {
        //get filename from hidden input from the UI
        $file = $request->input('filename');
        
        if(Auth::guard('school')->check())Log::channel('stakeholders_fileshares')->info(Auth::guard('school')->user()->code." download_file $file ".$fileshare->name);
        if(Auth::guard('teacher')->check())Log::channel('stakeholders_fileshares')->info(Auth::guard('teacher')->user()->afm." download_file $file ".$fileshare->name);

        return Storage::disk('local')->download($file);
    }

    /**
     * Delete a file from the fileshare.
     *
     * @param  Request  $request
     * @param  Fileshare  $fileshare
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete_file(Request $request, Fileshare $fileshare)
    {
        //get filename from hidden input from the UI
        $file = $request->input('filename');
        
        Storage::disk('local')->delete($file);
        $fn = basename($file);

        Log::channel('user_memorable_actions')->info(Auth::user()->username." delete_file $fn from ".$fileshare->name);
        return back()->with('success', "Το αρχείο $fn αφαιρέθηκε από τον διαμοιρασμό");
    }
}
