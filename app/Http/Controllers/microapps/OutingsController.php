<?php

namespace App\Http\Controllers\microapps;

use Throwable;
use App\Models\School;
use Illuminate\Http\Request;
use App\Models\microapps\Outing;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\microapps\OutingSection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OutingsController extends Controller
{
    //
    public function new_outing(Request $request){
        $school = Auth::guard('school')->user();
        $rule = [
            'record_file' => 'mimes:pdf'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return redirect(url('/index_school'))->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
        }
        $directory = 'outings';
        $file = $request->file('record_file');

        $outing_type = $request->all()['type'];
        $outing_destination = $request->all()['destination'];
        $outing_record = $request->all()['record'];
        $outing_file = $file->getClientOriginalName();
        // $outing_date = Carbon::parse($request->all()['outing_date']);
        $outing_date = $request->all()['outing_date'];
        try{
            $new_outing = Outing::create([
                'school_id'=>$school->id,
                'outingtype_id'=>$outing_type,
                'outing_date'=>$outing_date,
                'destination'=>$outing_destination,
                'record'=>$outing_record,
                'file'=>$outing_file,
                'checked'=>0   
            ]);
        }
        catch(Throwable $e){
            try{
                Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create outing error '.$e->getMessage());
            }
            catch(Throwable $e){
    
            }
            return redirect(url('/school_app/outings'))->with('failure', 'Δεν έγινε η καταχώρηση της εκδρομής, προσπαθήστε ξανά');
        }

        try{
            foreach($request->all() as $key=>$value){
                if(substr($key,0,7)=='section'){
                    OutingSection::create([
                        'outing_id' => $new_outing->id,
                        'section_id' => $value
                    ]); 
                }
            }
        }
        catch(Throwable $e){
            try{
                Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' match Section-Outing error '.$e->getMessage());
            }
            catch(Throwable $e){
    
            }
            return redirect(url('/school_app/outings'))->with('warning', 'Δεν έγινε η καταχώρηση των τμημάτων και του πρακτικού στην εκδρομή, μπορείτε να την επεξεργαστείτε και να προσπαθήσετε να τα εισάγετε ξανά');    
        }

        try{
            $path = $file->storeAs($directory, $school->code.'_'.$new_outing->id.'_'.$file->getClientOriginalName(), 'local');
        }
        catch (\Illuminate\Http\UploadedFile\FileSizeException $e) {
            // Handle file size exceeded exception
            throw new \Exception("File size exceeded: " . $e->getMessage());
        } 
        catch(Throwable $e){
            try{
                Log::channel('stakeholders_microapps')->error(Auth::guard('school')->user()->name." outing file upload error ".$e->getMessage());
            }
            catch(Throwable $e){
    
            }
            return redirect(url('/school_app/outings'))->with('warning', 'Δεν ανέβηκε το αρχείο στην εκδρομή, μπορείτε να την επεξεργαστείτε και να προσπαθήσετε να το ανεβάσετε ξανά');
        }
        // Log::channel('stakeholders_microapps')->info(Auth::guard('school')->user()->name." created outing ".$new_outing->id);
        return redirect(url('/school_app/outings'))->with('success','Η εκδρομή καταχωρίστηκε επιτυχώς');
    }

    public function download_record(Request $request, Outing $outing){
        if((Auth::check() && (Auth::user()->microapps->where('url', '/outings')->count() or Auth::user()->isAdmin())) || (Auth::guard('school')->check() && Auth::guard('school')->user()->id == $outing->school->id)){
            $file = 'outings/'.$outing->school->code.'_'.$outing->id.'_'.$outing->file;
            $response = Storage::disk('local')->download($file, $outing->file);  
            ob_end_clean();
            try{
                return $response;
            }
            catch(Throwable $e){
            
            return back()->with('failure', 'Δεν ήταν δυνατή η λήψη του αρχείου, προσπαθήστε ξανά');    
            }
        }
        abort(403, 'Unauthorized action.');
    }

    public function delete_outing(Request $request, Outing $outing){
        if((Auth::check() && (Auth::user()->microapps->where('url', 'outings')->count() or Auth::user()->isAdmin())) || (Auth::guard('school')->check() && Auth::guard('school')->user()->id == $outing->school->id)){    
            $file = 'outings/'.$outing->school->code.'_'.$outing->id.'_'.$outing->file;

            $outing->delete();
            try{
                Storage::disk('local')->delete($file);
            }
            catch(Throwable $e){
        
            }

            // return redirect(url('/school_app/outings'))->with('success','Η εκδρομή διαγράφηκε');
            return back()->with('success','Η εκδρομή διαγράφηκε');
        }
        abort(403, 'Unauthorized action.');
    }

    public function check_outing(Request $request, Outing $outing){
        if((Auth::check() && (Auth::user()->microapps->where('url', '/outings')->count() or Auth::user()->isAdmin()))){
            if($request->input('checked')=='true')
                $outing->checked = 1;
            else
                $outing->checked = 0;
            $outing->save();

            return response()->json(['message' => 'Outing updated successfully']);
        }
        abort(403, 'Unauthorized action.');
    }

    public function save_outing_profile(Request $request, Outing $outing){// protected by middleware
        $rule = [
            'record_file' => 'mimes:pdf'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return redirect(url('/index_school'))->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
        }

        // update the fields
        $outing->school_id = $outing->school->id;
        $outing->outingtype_id = $request->all()['type'];
        $outing->destination = $request->all()['destination'];
        $outing->record = $request->all()['record'];
        $outing->checked = 0;

        if($request->file('record_file')){
            //delete the old file
            $old_file = 'outings/'.$outing->school->code.'_'.$outing->id.'_'.$outing->file;
            try{
                Storage::disk('local')->delete($old_file);
            }
            catch(Throwable $e){
                try{
                    Log::channel('stakeholders_microapps')->error(Auth::guard('school')->user()->name." outing file delete error ".$e->getMessage());
                }
                catch(Throwable $e){
        
                }
            }
            //prepare the new file
            $file = $request->file('record_file');
            $directory = 'outings';
            
            //save the new file
            try{
                $path = $file->storeAs($directory, $outing->school->code.'_'.$outing->id.'_'.$file->getClientOriginalName(), 'local');
            }
            catch(Throwable $e){
                session(['warning' => 'Το νέο αρχείο της εκδρομής δεν αποθηκεύτηκε']);
            }

            //update the db file field
            $outing->file = $file->getClientOriginalName();
        }
        
        if($request->all()['outing_date'])
            $outing->outing_date = $request->all()['outing_date'];

        //empty the old OutingSection models
        foreach ($outing->sections as $out_sect){
            $out_sect->delete();
        }

        //create new OutingSection models
        foreach($request->all() as $key=>$value){
            try{
                if(substr($key,0,7)=='section'){
                    OutingSection::create([
                        'outing_id' => $outing->id,
                        'section_id' => $value
                    ]); 
                }
            }
            catch(Throwable $e){
                $name = Section::find($value)->name;
                try{
                    Log::channel('stakeholders_microapps')->error(Auth::guard('school')->user()->name." add section $value to outing error ".$e->getMessage());
                }
                catch(Throwable $e){
        
                }  
                session(['error' => "Το τμήμα $name δεν αποθηκεύτηκε στην εκδρομή"]);
            }
        }
        
        $outing->save();
        return redirect(url('/school_app/outings'))->with('success', 'Τα στοιχεία της εκδρομής ενημερώθηκαν');
    }
}
