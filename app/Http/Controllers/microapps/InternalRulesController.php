<?php

namespace App\Http\Controllers\microapps;

use Exception;
use App\Models\School;
use App\Models\Microapp;
use Illuminate\Http\Request;
use App\Mail\InternalRuleMail;
use App\Mail\InternalRuleCommented;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\microapps\InternalRule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InternalRulesController extends Controller
{
    //
    private $microapp;

    public function __construct(){
        $this->middleware('canViewMicroapp')->only(['create','store','index']);
        $this->microapp = Microapp::where('url', '/internal_rules')->first();
    }

    public function index(){
        return view('microapps.internal_rules.index', ['appname' => 'internal_rules']);
    }

    public function create(){
        if(Auth::guard('school')->check())
            return view('microapps.internal_rules.create-school', ['appname' => 'internal_rules']);
        else if(Auth::guard('consultant')->check())
            return view('microapps.internal_rules.create-consultant', ['appname' => 'internal_rules']);
    }

    public function store(Request $request){
        $school = Auth::guard('school')->user();
        if($this->microapp->accepts){
            $rule = [
                'int_rules_file' => 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];

            $validator = Validator::make($request->all(), $rule);
            if($validator->fails()){ 
                return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
            }
            $mimeType = $request->file('int_rules_file')->getClientMimeType();
            if($mimeType=="application/pdf"){
                $extension = ".pdf";
            }
            else if($mimeType=="application/msword"){
                $extension = ".doc";
            }
            else if($mimeType=="application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
                $extension = ".docx";
            }
            if($school->internal_rule){//update existing record
                if($school->internal_rule->consultant_comments_file xor $school->internal_rule->director_comments_file){//add second file
                    $db_field='school_file2';
                    $prefix = '2'; 
                }
                else if($school->internal_rule->consultant_comments_file and $school->internal_rule->director_comments_file){// add third file
                    $db_field='school_file3'; 
                    $prefix = '3';
                }
                else{//update first file
                    $db_field='school_file'; 
                    $prefix = '1';
                }
            }
            else{//create new record
                $db_field='school_file'; 
                $prefix = '1';
            }
            
            $file = $request->file('int_rules_file')->getClientOriginalName();
            //store the file
            $filename = "int_rules_".$school->code."_$prefix".$extension;
            try {
                $path = $request->file('int_rules_file')->storeAs('internal_rules', $filename);
            } catch (\Illuminate\Http\UploadedFile\FileSizeException $e) {
                // Handle file size exceeded exception
                throw new \Exception("File size exceeded: " . $e->getMessage());
            } catch (Exception $e) {
                // Handle other exceptions
                try {
                    Log::channel('stakeholders_microapps')->error(Auth::guard('school')->user()->name." create internal_rules file error ".$e->getMessage());
                } 
                catch(\Exception $e) {

                }
                return back()->with('failure', 'Δεν έγινε η αποθήκευση του αρχείου, προσπαθήστε ξανά');
            }

            try{
                InternalRule::updateOrCreate(
                [
                    'school_id'=>$school->id
                ],
                [
                    $db_field => $file,
                    'approved_by_consultant' => false,
                    'approved_by_director' => false
                ]); 
            }
            catch(\Exception $e){
                try{
                    Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create internal_rules db error '.$e->getMessage());
                }
                catch(\Exception $e){

                }
                return back()->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
            }
            return back()->with('success', 'Επιτυχής καταχώρηση αρχείου');   
        }
        else{
            return back()->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
        }
    }

    public function upload_director_comments_file(InternalRule $internal_rule, Request $request){
        $internal_rules_id = Microapp::where('url', '/internal_rules')->first()->id;
        if((Auth::check() && (Auth::user()->microapps->where('microapp_id', $internal_rules_id)->count() or Auth::user()->isAdmin()))){
            if($this->microapp->accepts){
                $rule = [
                    "director_comment_file" => 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ];

                $validator = Validator::make($request->all(), $rule);
                if($validator->fails()){ 
                    return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
                }
                $mimeType = $request->file("director_comment_file")->getClientMimeType();
                if($mimeType=="application/pdf"){
                    $extension = ".pdf";
                }
                else if($mimeType=="application/msword"){
                    $extension = ".doc";
                }
                else if($mimeType=="application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
                    $extension = ".docx";
                }

                $file = $request->file("director_comment_file")->getClientOriginalName();
                //store the file
                $school_code = $internal_rule->school->code;
                $filename = "int_rules_".$school_code."_dc".$extension;
                try{
                    $path = $request->file("director_comment_file")->storeAs('internal_rules', $filename);
                }
                catch(\Exception $e){
                    try{
                        Log::channel('stakeholders_microapps')->error(Auth::user()->username." upload director comments file error ".$e->getMessage());
                    }
                    catch(\Exception $e){

                    }
                    return back()->with('failure', 'Δεν έγινε η αποθήκευση του αρχείου, προσπαθήστε ξανά');     
                }
                
                $internal_rule->director_comments_file = $file;    
                try{
                   $internal_rule->save();
                }
                catch(\Exception $e){
                    try{
                        Log::channel('throwable_db')->error(Auth::user()->username.' update director comments file '.$e->getMessage());
                    }
                    catch(\Exception $e){

                    }
                    return back()->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
                }
                try{
                    Mail::to($internal_rule->school->mail)->send(new InternalRuleCommented('Διευθυντής Εκπαίδευσης', $internal_rule->school->md5));
                }
                catch(\Exception $e){
                    try{
                        Log::channel('mails')->error(Auth::user()->username." upload director comments file mail error ".$e->getMessage());
                    }
                    catch(\Exception $e){

                    }
                    return back()->with('failure', 'Το αρχείο ανέβηκε επιτυχώς, αλλά δεν έγινε η αποστολή του mail ειδοποίησης');     
                }
                return back()->with('success', 'Επιτυχής καταχώρηση αρχείου και αποστολή mail ειδοποίησης στο Σχολείο.');
            }
            else{
                return back()->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
            }
        }
        abort(403, 'Unauthorized action.');
    }

    public function upload_director_signed_file(InternalRule $internal_rule, Request $request){
        $internal_rules_id = Microapp::where('url', '/internal_rules')->first()->id;
        if((Auth::check() && (Auth::user()->microapps->where('microapp_id', $internal_rules_id)->count() or Auth::user()->isAdmin()))){
            if($this->microapp->accepts){
                $rule = [
                    "director_signed_file" => 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ];

                $validator = Validator::make($request->all(), $rule);
                if($validator->fails()){ 
                    return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
                }
                $mimeType = $request->file("director_signed_file")->getClientMimeType();
                if($mimeType=="application/pdf"){
                    $extension = ".pdf";
                }
                else if($mimeType=="application/msword"){
                    $extension = ".doc";
                }
                else if($mimeType=="application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
                    $extension = ".docx";
                }

                $file = $request->file("director_signed_file")->getClientOriginalName();
                //store the file
                $school_code = $internal_rule->school->code;
                $filename = "int_rules_".$school_code."_ds".$extension;
                try{
                    $path = $request->file("director_signed_file")->storeAs('internal_rules', $filename);
                }
                catch(\Exception $e){
                    try{
                        Log::channel('stakeholders_microapps')->error(Auth::user()->username." upload director signed file error ".$e->getMessage());
                    }
                    catch(\Exception $e){

                    }
                    return redirect(url('/admin/internal_rules'))->with('failure', 'Δεν έγινε η αποθήκευση του αρχείου, προσπαθήστε ξανά');     
                }

                $internal_rule->director_signed_file = $file;
                $internal_rule->director_signed_at = now();
                try{
                    $internal_rule->save();
                }
                catch(\Exception $e){
                    try{
                        Log::channel('throwable_db')->error(Auth::user()->username.' update director signed file '.$e->getMessage());
                    }
                    catch(\Exception $e){

                    }
                    return back()->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
                }
                try{
                    Mail::to($internal_rule->school->mail)->send(new InternalRuleMail());
                }
                catch(\Exception $e){
                    try{
                        Log::channel('mails')->error(Auth::user()->username." upload director signed file mail error ".$e->getMessage());
                    }
                    catch(\Exception $e){

                    }
                    return back()->with('failure', 'Το αρχείο ανέβηκε επιτυχώς, αλλά δεν έγινε η αποστολή του mail ειδοποίησης');     
                }
                return back()->with('success', 'Επιτυχής καταχώρηση αρχείου και αποστολή mail ειδοποίησης στο Σχολείο.');
            }
            else{
                return back()->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
            }
        }
        abort(403, 'Unauthorized action.');
    }

    public function upload_consultant_signed_file(InternalRule $internal_rule, Request $request){
        if(Auth::guard('consultant')->user()->schregion->id == $internal_rule->school->schregion->id) {
            if($this->microapp->accepts){
                $rule = [
                    "consultant_signed_file" => 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ];
                $validator = Validator::make($request->all(), $rule);
                if($validator->fails()){ 
                    return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
                }
                $mimeType = $request->file("consultant_signed_file")->getClientMimeType();
                if($mimeType=="application/pdf"){
                    $extension = ".pdf";
                }
                else if($mimeType=="application/msword"){
                    $extension = ".doc";
                }
                else if($mimeType=="application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
                    $extension = ".docx";
                }

                $file = $request->file("consultant_signed_file")->getClientOriginalName();
                //store the file
                $school_code = $internal_rule->school->code;
                $filename = "int_rules_".$school_code."_cs".$extension;
                try{
                    $path = $request->file("consultant_signed_file")->storeAs('internal_rules', $filename);
                }
                catch(\Exception $e){
                    try{
                        Log::channel('stakeholders_microapps')->error(Auth::user()->username." upload consultant signed file error ".$e->getMessage());
                    }
                    catch(\Exception $e){

                    }
                    return back()->with('failure', 'Δεν έγινε η αποθήκευση του αρχείου, προσπαθήστε ξανά');     
                }

                $internal_rule->consultant_signed_file = $file;
                $internal_rule->consultant_signed_at = now();    
                try{
                    $internal_rule->save();
                }
                catch(\Exception $e){
                    try{
                        Log::channel('throwable_db')->error(Auth::user()->username.' update consultant signed file '.$e->getMessage());
                    }
                    catch(\Exception $e){

                    }
                    return back()->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
                }
                return back()->with('success', 'Επιτυχής καταχώρηση αρχείου');
            }
            else{
                return back()->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
            }
        }
        abort(403, 'Unauthorized action.');
    }

    public function upload_consultant_comments_file(InternalRule $internal_rule, Request $request){
        if(Auth::guard('consultant')->user()->schregion->id == $internal_rule->school->schregion->id) {    
            if($this->microapp->accepts){
                $rule = [
                    "consultant_comment_file" => 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ];

                $validator = Validator::make($request->all(), $rule);
                if($validator->fails()){ 
                    return back()->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
                }
                $mimeType = $request->file("consultant_comment_file")->getClientMimeType();
                if($mimeType=="application/pdf"){
                    $extension = ".pdf";
                }
                else if($mimeType=="application/msword"){
                    $extension = ".doc";
                }
                else if($mimeType=="application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
                    $extension = ".docx";
                }

                $file = $request->file("consultant_comment_file")->getClientOriginalName();
                //store the file
                $school_code = $internal_rule->school->code;
                $filename = "int_rules_".$school_code."_cc".$extension;
                try{
                    $path = $request->file("consultant_comment_file")->storeAs('internal_rules', $filename);
                }
                catch(\Exception $e){
                    try{
                        Log::channel('stakeholders_microapps')->error(Auth::user()->username." upload consultant comments file error ".$e->getMessage());
                    }
                    catch(\Exception $e){

                    }
                    return back()->with('failure', 'Δεν έγινε η αποθήκευση του αρχείου, προσπαθήστε ξανά');     
                }

                $internal_rule->consultant_comments_file = $file;
                try{
                    $internal_rule->save();
                }
                catch(\Exception $e){
                    try{
                        Log::channel('throwable_db')->error(Auth::user()->username.' update consultant comments file '.$e->getMessage());
                    }
                    catch(\Exception $e){

                    }
                    return back()->with('failure', 'Δεν έγινε η καταχώρηση, προσπαθήστε ξανά');    
                }
                try{
                    Mail::to($internal_rule->school->mail)->send(new InternalRuleCommented('Σύμβουλος Εκπαίδευσης', $internal_rule->school->md5));
                }
                catch(\Exception $e){
                    try{
                        Log::channel('mails')->error(Auth::user()->username." upload consultant comments file mail error ".$e->getMessage());
                    }
                    catch(\Exception $e){

                    }
                    return back()->with('failure', 'Το αρχείο ανέβηκε επιτυχώς, αλλά δεν έγινε η αποστολή του mail ειδοποίησης');     
                }
                return back()->with('success', 'Επιτυχής καταχώρηση αρχείου και αποστολή mail ειδοποίησης στο Σχολείο.');
            }
            else{
                return back()->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
            }
        }
        abort(403, 'Unauthorized action.');
    }

    public function download_file(InternalRule $internal_rule, $file_type){
        // code for auth checks here
        //...........

        //find the extension
        $filename = $internal_rule->$file_type;
        $lastDotPos = strrpos($filename, '.');
        $extension = substr($filename, $lastDotPos);
    
        switch($file_type){
            case 'school_file':
                $file = "internal_rules/int_rules_".$internal_rule->school->code."_1".$extension;
                break;
            case 'school_file2':
                $file = "internal_rules/int_rules_".$internal_rule->school->code."_2".$extension;
                break;
            case 'school_file3':
                $file = "internal_rules/int_rules_".$internal_rule->school->code."_3".$extension;
                break;
            case 'consultant_comments_file':
                $file = "internal_rules/int_rules_".$internal_rule->school->code."_cc".$extension;
                break;
            case 'director_comments_file':
                $file = "internal_rules/int_rules_".$internal_rule->school->code."_dc".$extension;
                break;
            case 'consultant_signed_file':
                $file = "internal_rules/int_rules_".$internal_rule->school->code."_cs".$extension;
                break;
            case 'director_signed_file':
                $file = "internal_rules/int_rules_".$internal_rule->school->code."_ds".$extension;
                break;
        }
        $response = Storage::disk('local')->download($file, $filename);
        ob_end_clean();
        try{
            return $response; 
        }
        catch(\Exception $e){
            return back()->with('failure', 'Δεν ήταν δυνατή η λήψη του αρχείου, προσπαθήστε ξανά');    
        }
    }

    public function check(Request $request, InternalRule $internal_rule){
        // code for auth checks here
        //...........
        if($request->input('checked') == 'directorYes'){
            $internal_rule->approved_by_director = 1;
        }    
        if($request->input('checked') == 'directorNo'){
            $internal_rule->approved_by_director = 0;   
        }
        if($request->input('checked') == 'consultantYes'){
            $internal_rule->approved_by_consultant = 1;
        }    
        if($request->input('checked') == 'consultantNo'){
            $internal_rule->approved_by_consultant = 0;   
        } 
        $internal_rule->save();

        return response()->json(['message' => 'Internal Rule updated successfully']);
    }
}
