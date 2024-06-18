<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Filecollect;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\FilecollectStakeholder;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FilecollectController extends Controller
{
    //
    public function __construct(){
        $this->middleware('auth')->only(['index','store']);
    }

    public function index(){
        return view('filecollects.index');
    }

    public function edit(Filecollect $filecollect){
        $this->authorize('view', $filecollect);
        return view('filecollects.edit', [
            'filecollect' => $filecollect
        ]);
    }

    public function show(Filecollect $filecollect){
        if(Auth::guard('teacher')->check()){
            $stakeholder = $filecollect->stakeholders->where('stakeholder_id', Auth::guard('teacher')->id())->where('stakeholder_type', 'App\Models\Teacher')->first();
            if(!$stakeholder or !$filecollect->visible){
                abort(403);
            }
            return view('filecollects.teacher-filecollect', ['filecollect' => $filecollect]);
        }
        else if(Auth::guard('school')->check()){
            $stakeholder = $filecollect->stakeholders->where('stakeholder_id', Auth::guard('school')->id())->where('stakeholder_type', 'App\Models\School')->first();
            if(!$stakeholder or !$filecollect->visible){
                abort(403);
            }
            return view('filecollects.school-filecollect', ['filecollect' => $filecollect]);
        }
        else{
            abort(403);
        }
    }

    public function store(Request $request)
    {
        $filecollect_table = $this->validate_and_prepare($request);
        $result = $this->create_filecollect($filecollect_table);
        if($result->getStatusCode() == 200){
            $filecollect = Filecollect::find($result->getData()->filecollect);
            Log::channel('user_memorable_actions')->info(Auth::user()->username." insert_filecollect ".$filecollect->name);
            return redirect(url("/filecollects/$filecollect->id/edit"))->with('success', 'Η συλλογή αρχείων δημιουργήθηκε με επιτυχία. Μπορείτε να προσθέσετε ενδιαφερόμενους στη συνέχεια. Μην ξεχάσετε να "ανοίξετε" την υποβολή!'); 
        }
        else{
            Log::channel('throwable_db')->error(Auth::user()->username." insert_filecollect: ".$e->getMessage());
            return redirect(url('/filecollects'))->with('failure', 'Κάποιο πρόβλημα προέκυψε (throwable_db). Η Συλλογή Αρχείων δε δημιουργήθηκε.');
        }
    }

    public function update_admin_file(Request $request, Filecollect $filecollect, $type){
        if($type=='base'){
            $file = $request->file('base_file');
            $msg = "Η εγκύκλιος";
        }
        else{
            $file = $request->file('template_file');
            $msg = "Το πρότυπο";
        }
        $upload_result = $this->upload_file($file, $filecollect->id);
        if(isset($upload_result->getData()->success)){
            if($type=='base')
                $filecollect->base_file = $file->getClientOriginalName();
            else
                $filecollect->template_file = $file->getClientOriginalName();
            $filecollect->save();
        }
        else{
            Log::channel('files')->error(Auth::user()->username." filecollect $filecollect->id update $type: ".$e->getMessage());
            return back()->with('failure', 'Κάποιο πρόβλημα προέκυψε (files). Ενημερώστε τον διαχειριστή του συστήματος.');
        }
        Log::channel('files')->info(Auth::user()->username." filecollect $filecollect->id update $type success");
        return back()->with('success', $msg.' ενημερώθηκε');
    }

    private function upload_file($file, $filecollect_id){//app_use
        $error=false;
        $directory = 'file_collects/'.$filecollect_id;
        // store  file
        $fileHandler = new FilesController();
        $upload  = $fileHandler->upload_file($directory, $file, 'local');
        if($upload->getStatusCode() == 500){
            $error=true;
        }
        if(!$error)
            return response()->json([
                'success' => "File uploaded"
            ]);
        else
            return response()->json([
                'error' => "Files not uploaded"
            ]);
    }

    public function update_comment(Request $request, Filecollect $filecollect){
        $validator = Validator::make($request->all(), [
            'comment' => 'max:5000',
        ]);
        if($validator->fails()){
            return back()->with('warning', 'Το σχόλιό σας ξεπερνάει το όριο των 5000 χαρακτήρων');
        }
        $comment= $request->input('comment');
        $sanitizedComment = strip_tags($comment, '<p><a><b><i><u><ul><ol><li>'); //allow only these tags
        $filecollect->comment = $sanitizedComment;
        $filecollect->save();
        Log::channel('user_memorable_actions')->info(Auth::user()." updated filecollect $filecollect->id comment");
        return back()->with('success', 'Το σχόλιο αποθηκεύτηκε');    
    }

    private function validate_and_prepare(Request $request){ //page use
        if($request->user()->can('chooseDepartment', Filecollect::class)){
            $department_id = $request->input('department');
        }
        else{
            $department_id = $request->user()->department->id;
        }
        if($request->all()['no_of_files'] > 5)
            $no_of_files = 5;
        else
            $no_of_files = $request->all()['no_of_files'];

        $table = array();
        $table['name'] = $request->all()['filecollect_name'];
        $table['department_id'] = $department_id;
        $table['fileMime'] = 'nothing';
        $table['visible'] = 0;
        $table['accepts'] = 0;
        $table['no_of_files'] = $no_of_files;
        return $table;
    }

    private function create_filecollect($table){ //app use
        try{
            $filecollect = Filecollect::create($table);
        }
        catch(Throwable $e){
            return response()->json([
                'error' => 'Filecollect creation failed'
            ], 500);
        }
        return response()->json([
            'success' => 'Filecollect created successfully',
            'filecollect' => $filecollect->id
        ], 200);
    }

    public function update(Filecollect $filecollect, Request $request){
        $this->authorize('view', $filecollect);
        $incomingFields = $request->all();
        if($incomingFields['no_of_files']!= $incomingFields['no_of_pdf_files']+ $incomingFields['no_of_docx_files']+ $incomingFields['no_of_xlsx_files']){
            return back()->with('failure', 'Ο αριθμός των αρχείων δεν ταιριάζει με τον αριθμό των αρχείων pdf, docx και xlsx');
        }
        $filecollect->fileMime = json_encode(array('pdf'=>$incomingFields['no_of_pdf_files'], 'docx'=>$incomingFields['no_of_docx_files'], 'xlsx'=>$incomingFields['no_of_xlsx_files']));
        $filecollect->name = $incomingFields['name'];
        $filecollect->no_of_files = $incomingFields['no_of_files'];
        $edited=false;
            
        // check if changes happened to filecollect table
        if($filecollect->isDirty()){
            // if name has changed
            if($filecollect->isDirty('name')){
                $given_name = $incomingFields['name'];

                // if there is already a filecollect with the new given name
                if(Filecollect::where('name', $given_name)->count()){
                    return back()->with('failure',"Υπάρχει ήδη συλλογή αρχείων με όνομα $given_name.");
                } 
            }
            
            if($filecollect->isDirty('no_of_files')){
                if($filecollect->no_of_files > 5)
                    $filecollect->no_of_files = 5;
            }
            $filecollect->save();
            $edited = true;
        }
        Log::channel('user_memorable_actions')->info(Auth::user()." updated filecollect $filecollect->id basic info");
        return back()->with('success',"Επιτυχής αποθήκευση των στοιχείων της Συλλογής $filecollect->name");
    }

    public function change_status(Request $request, Filecollect $filecollect){
        if($request->all()['asks_to'] == 'ch_vis_status'){
            $filecollect->visible = $filecollect->visible==1?0:1; //change visibility based on previous state
            $filecollect->accepts = 0; // reset acceptability
            $filecollect->save();
            Log::channel('user_memorable_actions')->info(Auth::user()->username." changeFilecollectStatus (change visibility) ".$filecollect->name);
        }
        if($request->all()['asks_to'] == 'ch_acc_status'){
            $filecollect->accepts = $filecollect->accepts==1?0:1; // change acceptability based on previous state
            $filecollect->save();
            Log::channel('user_memorable_actions')->info(Auth::user()->username." changeFilecollectStatus (change acceptability) ".$filecollect->name);
        }
        return back()->with('success', 'H κατάσταση της εφαρμογής άλλαξε επιτυχώς');
    }

    public function upload_stake_file(Request $request, Filecollect $filecollect){
        if($filecollect->visible and $filecollect->accepts){
            $record_to_update=null;
            //identify stakeholder
            if(Auth::guard('school')->check()){
                $record_to_update = Auth::guard('school')->user()->filecollects->where('filecollect_id', $filecollect->id)->first();
                $identifier = Auth::guard('school')->user()->code;
            }
            else if(Auth::guard('teacher')->check()){
                $record_to_update = Auth::guard('teacher')->user()->filecollects->where('filecollect_id', $filecollect->id)->first(); 
                $identifier = Auth::guard('teacher')->user()->afm;   
            }
        
            if(!$record_to_update){
                abort(403);
            }
            else{
                //validate the input file
                $pdf = json_decode($filecollect->fileMime, true)['pdf'];
                $docx = json_decode($filecollect->fileMime, true)['docx'];
                $xlsx = json_decode($filecollect->fileMime, true)['xlsx'];
                
                $types_and_mimes = [['type'=>'pdf', 'mime'=>'application/pdf'], ['type'=>'docx', 'mime'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document'] , ['type'=>'xlsx', 'mime'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']];
                foreach($types_and_mimes as $type_and_mime){
                    $t = $type_and_mime['type'];
                    $m = $type_and_mime['mime'];
                    if($$t){
                        for ($i = 1; $i<=$$t; $i++) {
                            $rule["the_file_".$t.$i] = "file|required|max:3000|mimetypes:".$m;
                        }
                        $validator = Validator::make($request->all(), $rule);
                        if($validator->fails()){ 
                            return back()->with('failure', $validator->errors()->first());
                        }
                    }
                }
                $types = ['pdf', 'docx', 'xlsx'];
                $files_array_for_db =[];
                $index = 1;
                foreach($types as $type){
                    $orginalFilenamesArray = []; //an array to check if the file already exists
                    for($i = 1; $i <= $$type; $i++ ){
                        $file = $request->file("the_file_$type".$i);
                        $originalFilename = $file->getClientOriginalName();

                        // check if the file already exists
                        if(in_array($originalFilename, $orginalFilenamesArray))
                            return back()->with('failure', 'Το αρχείο '.$originalFilename.' υπάρχει ήδη');

                        $orginalFilenamesArray[] = $originalFilename; //add the filename to the array for later check

                        $extension = $file->getClientOriginalExtension();
                        $filename = $identifier . "_filecollect" . $filecollect->id . "(". $i . ")" . "." . $extension;
                        try {
                            $path = $file->storeAs("file_collects/$filecollect->id", $filename);
                        } catch (\Exception $e) {
                            Log::channel('files')->error($identifier . ' '. $originalFilename. ' failure to upload file for filecollect ' . $filecollect->id . ' ' . $e->getMessage());
                            continue;
                        }
                        $files_array_for_db[] = [
                            'index' => $index++,
                            'filename' => $filename,
                            'original_filename' => $originalFilename,
                        ];
                        Log::channel('files')->info($identifier . ' '. $originalFilename. ' success to upload file for filecollect ' . $filecollect->id);
                    }
                }
                //prepare the record to update and save it
                $record_to_update->file = json_encode($files_array_for_db, JSON_UNESCAPED_UNICODE);
                $record_to_update->uploaded_at = Carbon::now();
                $record_to_update->checked = false;
                try{
                    $record_to_update->save();
                }
                catch(Exception $e){
                    Log::channel('throwable_db')->error($identifier.' failure to update database for filecollect '. $filecollect->id.' '.$e->getMessage()); 
                    return back()->with('failure', 'Η ενέργεια απέτυχε (throwable_db). Επικοινωνήστε με τον διαχειριστή του συστήματος');  
                }
                return back()->with('success', 'Η ενέργεια ολοκληρώθηκε!');  
            }
        }
        else
            abort('403');
    }

    public function download_stake_file(Request $request, FilecollectStakeholder $old_data, $filename){
        if(get_class($old_data->stakeholder) == 'App\Models\School')
            $identifier = $old_data->stakeholder->code;
        else if(get_class($old_data->stakeholder) == 'App\Models\Teacher')
            $identifier = $old_data->stakeholder->afm;

        //find the saved filename
        $files = json_decode($old_data->file, true);
        $fileData = array_filter($files, function($file) use ($filename) {
           return $file['original_filename'] == $filename;
        });
        if (!empty($fileData)) {
            $fileData = reset($fileData);
            $file = "file_collects/".$old_data->filecollect->id."/".$fileData['filename'];
        }
        else 
            return back()->with('failure', 'Το αρχείο δεν υπάρχει.');

        if(Storage::disk('local')->exists($file)){
            $response = Storage::disk('local')->download($file, $filename);  
            ob_end_clean();
            try{
                return $response;
            }
            catch(\Exception $e){
                return back()->with('failure', 'Δεν ήταν δυνατή η λήψη του αρχείου, προσπαθήστε ξανά');    
            }
        } 
        else 
            return back()->with('failure', 'Το αρχείο δεν υπάρχει.');
    }
    
    public function check_uncheck(Request $request, FilecollectStakeholder $stakeholder){
        $filecollect = Filecollect::find($stakeholder->filecollect_id);
        if(Auth::user()->department->filecollects->find($filecollect->id)){
            if($request->input('checked')=='true')
                $stakeholder->checked = 1;
            else
                $stakeholder->checked = 0;
            $stakeholder->save();

            return response()->json(['message' => 'Filecollect updated successfully']);
        }
        else abort(403);
    }

    public function destroy(Filecollect $filecollect){
        $this->authorize('view', $filecollect);
        $username = Auth::check() ? Auth::user()->username : "API";
        $error=false;
        // delete database record
        try{
            Filecollect::destroy($filecollect->id);
        }
        catch(\Exception $e){
            Log::channel('throwable_db')->error($username."failed to delete_filecollect: ".$e->getMessage());
            return back()->with('failure', 'Ο διαμοιρασμός αρχείων δεν διαγράφηκε (throwable_db)');
        }
        
        //delete files from disk
        $directoryHandler = new FilesController();
        $directory = 'file_collects/'.$filecollect->id;
        $delete_directory = $directoryHandler->delete_directory($directory, 'local');
        if($delete_directory->getStatusCode() == 500){
            Log::channel('files')->error($username." Filecollect directory $directory failed to delete");
            $error=true;
        }
        else
            Log::channel('files')->info($username." Filecollect directory $directory deleted successfully");
        Log::channel('user_memorable_actions')->info($username." delete_filecollect ".$filecollect->name);
        if(!$error){
            return redirect(url('/filecollects'))->with('success', "Η κοινοποίηση αρχείων $filecollect->name διαγράφηκε");
        }
        else{
            return redirect(url('/filecollects'))->with('warning', "Η κοινοποίηση αρχείων $filecollect->name διαγράφηκε με σφάλματα (files)");
        }
    }

    public function delete_stakeholder_file(FilecollectStakeholder $stakeholder){
        if($stakeholder->filecollect->accepts){
            if(Auth::guard('school')->check()){
                $identifier = Auth::guard('school')->user()->code;
            }
            else if (Auth::guard('teacher')->check()){
                $identifier = Auth::guard('teacher')->user()->afm;
            }

            $directory = "file_collects/$stakeholder->filecollect_id";

            // Find the filename in the JSON string that matches the 'original_filename'
            $files = json_decode($stakeholder->file, true);
            foreach($files as $file){
                // dd($file);
                $fileHandler = New FilesController;
                try{
                    $fileHandler->delete_file($directory, $file['filename'], 'local');
                }
                catch(\Exception $e){
                    Log::channel('files')->error($identifier." failed to delete file from filecollect $stakeholder->filecollect_id ".$e->getMessage());
                    return back()->with('failure', 'Το αρχείο δε διαγράφηκε, προσπαθήστε αργότερα ή επικοινωνήστε με τον διαχειριστή του συστήματος');
                }
            }
            $stakeholder->file = null;
            $stakeholder->checked = false;
            $stakeholder->uploaded_at = null;
            $stakeholder->save();

            Log::channel('files')->info($identifier." successfully deleted file from filecollect $stakeholder->filecollect_id");
            return back()->with('success', 'Τα αρχεία διαγράφηκαν');
        }
        else abort(403);
    }

    public function save_stake_comment(Request $request, FilecollectStakeholder $stakeholder){
        if(Auth::guard('school')->check()){
            $user = Auth::guard('school')->user();
        }
        else if(Auth::guard('teacher')->check()){
            $user = Auth::guard('teacher')->user();
        }
        if($user->filecollects->find($stakeholder->id)){
            $sanitizedComments = strip_tags($request->input('stake_comment'), '<p><a><b><i><u><ul><ol><li>'); //allow only these tags
            $stakeholder->stake_comment = $sanitizedComments;
            if($stakeholder->isDirty('stake_comment'))
                $stakeholder->save();

            return response()->json(['success'=>'comments saved'], 200);
        }
        else abort(403);
    }

    public function download_directory(Request $request, Filecollect $filecollect){
        $directory = 'file_collects/' . $filecollect->id;
        $helper = new FilesController;
        $files = $helper->download_directory_as_zip($directory);

        if($files->getStatusCode()=='500'){
            Log::channel('files')->error(Auth::user()->username." failed to download filecollect $filecollect->id: ".json_decode($files->getContent(),true)['error']);
            return back()->with('failure', json_decode($files->getContent(),true)['error'].'. Επικοινωνήστε με τον διαχειριστή. ');
        }

        Log::channel('files')->info(Auth::user()->username." successfully downloaded filecollect $filecollect->id");
        if (ob_get_length()) {
            ob_end_clean();
        }
        return $files;
    }

    public function add_num_of_lines(Request $request, Filecollect $filecollect){
        $validator = Validator::make($request->all(), [
            'lines' => 'integer|min:0|'
        ]);
        if($validator->fails()){
            return back()->with('failure', 'Ο αριθμός γραμμών πρέπει να είναι ακέραιος αριθμός μεγαλύτερος του 0');
        }
        $filecollect->lines_to_extract = $request->input('lines');
        $filecollect->save();

        return back()->with('success', 'Ο αριθμός γραμμών αποθηκεύτηκε');
    }

    public function extract_xlsx_file(Request $request, Filecollect $filecollect){
        $directory = storage_path("app/file_collects/$filecollect->id");
        $files = scandir($directory);
        $excelFiles = array_filter($files, function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'xlsx';
        });

        $spreadsheetOutput = new Spreadsheet();
        $sheetOutput = $spreadsheetOutput->getActiveSheet();
        $new_sheet_row = 1;

        //regural expressions to identify the stakeholder from the file name
        $regex6 = '/(?<!\d)\d{6}(?!\d)/';
        $regex9 = '/(?<!\d)\d{9}(?!\d)/';
        $regex7 = '/(?<!\d)\d{7}(?!\d)/';
        foreach ($excelFiles as $excelFile) {
            if ($excelFile !== $filecollect->base_file && $excelFile !== $filecollect->template_file) {
                $filePath = "$directory/$excelFile";
                if (preg_match($regex9, $filePath, $matches)) {
                    $stakeholder = Teacher::where('afm', $matches[0])->first();//the number is teacher afm
                } 
                else if (preg_match($regex6, $filePath, $matches)) {
                    $stakeholder = Teacher::where('am', $matches[0])->first();//the number is teacher am
                }
                else if (preg_match($regex7, $filePath, $matches)) {
                    $stakeholder = School::where('code', $matches[0])->first();//the number is school code
                }
                try{
                    $reader = IOFactory::createReader('Xlsx');
                    $reader->setReadDataOnly(true);
                    $spreadsheetInput = $reader->load($filePath);
                    $worksheet = $spreadsheetInput->getActiveSheet();

                    $linesToExtract = $filecollect->lines_to_extract;
                    // Copy the specified number of lines to the new spreadsheet
                    for ($row = 2; $row <= $linesToExtract + 1; $row++) {
                        $rowData = [];

                        if ($stakeholder instanceof Teacher) {
                            $rowData[] = 'Εκπαιδευτικός';
                            $rowData[] = $stakeholder->afm;
                            $rowData[] = $stakeholder->name.' '.$stakeholder->surname;
                        } 
                        else if ($stakeholder instanceof School) {
                            $rowData[] = 'Σχολείο';
                            $rowData[] = $stakeholder->code;
                            $rowData[] = $stakeholder->name;
                        }

                        $rowData = array_merge($rowData, $worksheet->rangeToArray("A{$row}:Z{$row}")[0]);

                        $sheetOutput->fromArray($rowData, null, "A{$new_sheet_row}");
                        $new_sheet_row++;
                    }
                }
                catch(\Exception $e){
                    Log::channel('files')->error(Auth::user()->username." failed to extract file $filePath: ".$e->getMessage());
                }
                finally{
                    unset($reader);
                    $spreadsheetInput->disconnectWorksheets();
                    unset($spreadsheetInput);
                    unset($worksheet); // Unset the $worksheet variable to free up memory
                    gc_collect_cycles();
                }
            }
        }

        $writer = new Xlsx($spreadsheetOutput);
        $newFilePath = "{$directory}/filecollect".$filecollect->id."_extracted_data.xlsx";
        $writer->save($newFilePath);
        $spreadsheetOutput->disconnectWorksheets();
        unset($spreadsheetOutput);
        unset($writer);
        gc_collect_cycles();
        ob_end_clean();
        return response()->download($newFilePath)->deleteFileAfterSend(true);
    }

    public function send_personal_message(Request $request){
        $stakeholder = FilecollectStakeholder::find($request->input('stakeholder_id'));
        $stakeholder->message_from_admin = $request->input('message');
        $stakeholder->message_from_admin_at = Carbon::now();
        $stakeholder->save();

        return back()->with('success', 'Το μήνυμα εστάλη');
        // return back()->with('success', $stakeholder->stakeholder->name);
    }

    public function download_admin_file(Filecollect $filecollect, $type){
        if($type == 'base'){
            $filename = $filecollect->base_file;
        }
        else if($type=='template'){
            $filename = $filecollect->template_file;
        }
        $file = "file_collects/".$filecollect->id."/".$filename;
        if(Storage::disk('local')->exists($file)){
            $response = Storage::disk('local')->download($file);  
            ob_end_clean();
            try{
                return $response;
            }
            catch(\Exception $e){
                return back()->with('failure', 'Δεν ήταν δυνατή η λήψη του αρχείου, προσπαθήστε ξανά');    
            }
        } 
        else 
            return back()->with('failure', 'Το αρχείο δεν υπάρχει.');  
    }
}