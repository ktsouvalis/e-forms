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
    /**
     * Insert a new fileshare into database, set it's directories.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function insert_fileshare(Request $request)
    {
        // create a database record
        $newFileshare = Fileshare::create([
            'name' => $request->all()['fileshare_name'],
            'department_id' => Auth::user()->department->id
        ]);

        // set the directories for common and personal files
        $directory_common = 'fileshare'.$newFileshare->id;
        $directory_personal = $directory_common.'/personal_files';
        
        $common_files = $request->file('fileshare_common_files');

        // store common files if any
        if ($common_files) {
            foreach ($common_files as $file) {
                $path = $file->storeAs($directory_common, $file->getClientOriginalName(), 'local');
            }
        }

        $personal_files = $request->file('fileshare_personal_files');

        //store personal files if any
        if ($personal_files) {
            foreach ($personal_files as $file) {
                $path = $file->storeAs($directory_personal, $file->getClientOriginalName(), 'local');
            }
        }

        return redirect(url('/fileshares'))->with('success', 'Ο διαμοιρασμός αρχείων δημιουργήθηκε. Μπορείτε να προσθέσετε και άλλα αρχεία στη συνέχεια.');
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

        // Update name
        $fileshare->name = $request->all()['name'];
        if ($fileshare->isDirty('name')) {
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
        }

        $personal_files = $request->file('fileshare_personal_files');

        // store personal files if any
        if ($personal_files) {
            foreach ($personal_files as $file) {
                $path = $file->storeAs($directory_personal, $file->getClientOriginalName(), 'local');
            }
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
        // delete database record
        Fileshare::destroy($fileshare->id);

        //delete files from disk
        Storage::disk('local')->deleteDirectory('fileshare'.$fileshare->id);

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

        return back()->with('success', "Το αρχείο $fn αφαιρέθηκε από τον διαμοιρασμό");
    }
}
