<?php

namespace App\Http\Controllers\microapps;

use Exception;
use Throwable;
use App\Models\User;
use App\Models\Month;
use App\Models\School;
use App\Models\Microapp;
use App\Models\Superadmin;
use App\Mail\TicketCreated;
use App\Mail\TicketUpdated;
use Illuminate\Http\Request;
use App\Models\microapps\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\microapps\TicketPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\microapps\AllDaySchool;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Validator;

class TicketsController extends Controller
{
    //
    private $microapp;

    public function __construct(){
        $this->middleware('auth')->only(['index']);
        $this->middleware('isSchool')->only(['create', 'store']);
        $this->middleware('canUpdateTicket')->only(['update']);
        $this->microapp = Microapp::where('url', '/tickets')->first();
    }

    public function index(){
        return view('microapps.tickets.index', ['appname' => 'tickets']);
    }

    public function create(){
        return view('microapps.tickets.create', ['appname' => 'tickets']);
    }

    public function edit(Ticket $ticket){
        if(Auth::check()){
            $ticket->unread_by_admin=0;
            $ticket->save();
            return view('microapps.tickets.ticket-profile-admin', compact('ticket'));
        }
        else if(Auth::guard('school')->check()){
            $ticket->unread_by_school=0;
            $ticket->save();
            return view('microapps.tickets.ticket-profile-school', compact('ticket'));
        }
    }

    private function validate_request(Request $request){
        $string="";
        $error=false;
        $validator = Validator::make($request->all(), [
            'comments' => 'required_without:attachment|max:5000',
            'attachment' => 'required_without:comments|file|max:10240|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/jpeg,image/png,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document' 
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $error=true;
            if($validator->errors()->has('comments')){
                foreach($validator->errors()->get('comments') as $message){
                    $string = $string.$message." ";
                }
            }
            if($validator->errors()->has('attachment')){
                foreach($validator->errors()->get('attachment') as $message){
                    if(strpos($message, 'application'))
                        $string = $string."Το αρχείο πρέπει να είναι τύπου xlsx, docx, pdf, jpg ή png ";
                    else{
                        $string = $string.$message." ";
                    }
                }
            }
        }
        return ['error'=>$error, 'message'=>$string];
    }

    private function create_db_entry($subject, $comments, $school_id=null){
        if($school_id==null)
            $school_id = Auth::guard('school')->user()->id;
        $sanitizedComments = strip_tags($comments, '<p><a><b><i><u><ul><ol><li>'); //allow only these tags
        try{
            $new_ticket = Ticket::create([
                'school_id' => $school_id,
                'subject' => $subject,
                'comments' => $sanitizedComments,
                'solved' => 0,
                'unread_by_admin' => 1,
                'unread_by_school' => 0
            ]); 
        }
        catch(Throwable $e){
            Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create ticket db error '.$e->getMessage());
            return 'error';
        }
        Log::channel('tickets')->info($new_ticket->school->name." ticket $new_ticket->id creation success");
        return $new_ticket;    
    }

    private function send_creation_mails(Ticket $new_ticket){
        $success=true;

        foreach(Superadmin::all() as $superadmin){
            $superadmin->user->notify(new UserNotification("Δείτε <a href='".route('tickets.edit', ['ticket' => $new_ticket->id])."'>εδώ</a> το δελτίο", "Νέο δελτίο $new_ticket->id"));
        }

        try{
            Mail::to($new_ticket->school->mail)->send(new TicketCreated($new_ticket));
        }
        catch(Throwable $e){
            $success=false;
            Log::channel('tickets')->info("new ticket ".$new_ticket->id." mail failure to school ".$e->getMessage()); 
        }
        return $success;
    }

    private function send_update_mails(Ticket $ticket, $new_string){
        $success=true;

        foreach(Superadmin::all() as $superadmin){
            $superadmin->user->notify(new UserNotification("Δείτε <a href='".route('tickets.edit', ['ticket' => $ticket->id])."'>εδώ</a> το δελτίο", 'Ανανέωση δελτίου '.$ticket->id));
        }

        try{
             Mail::to($ticket->school->mail)->send(new TicketUpdated($ticket, $new_string, $ticket->school->name, $ticket->school->md5));;
        }
        catch(Throwable $e){
            $success=false;
            Log::channel('tickets')->info("update ticket ".$ticket->id." mail failure to school ".$e->getMessage()); 
        }
        return $success;
    }
        
    public function store(Request $request){
        $validation = $this->validate_request($request);
        if($validation['error']==true){
            return back()->with('failure', $validation['message']);
        }

        $subject = $request->input('subject');
        $comments = $request->input('comments');
        $new_ticket = $this->create_db_entry($subject, $comments);

        if($new_ticket!='error'){
            $mails = $this->send_creation_mails($new_ticket);
        }

        if($mails)
            return redirect()->route('tickets.edit', ['ticket' => $new_ticket->id])->with('success','Το δελτίο δημιουργήθηκε με επιτυχία!');  
        else
            return redirect()->route('tickets.edit', ['ticket' => $new_ticket->id])->with('warning','Το δελτίο δημιουργήθηκε με επιτυχία, κάποια mail απέτυχαν να σταλούν');       
        
    }

    private function resolve_in_db(Ticket $ticket){
        $ticket->solved=1;
        try{
            $ticket->save();
        }
        catch(\Exception $e){
            Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' resolve ticket db error '.$e->getMessage());
            return false;
        }
        return true;
    }

    private function open_in_db(Ticket $ticket){
        $ticket->solved=0;
        try{
            $ticket->save();
        }
        catch(\Exception $e){
            Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' resolve ticket db error '.$e->getMessage());
            return false;
        }
        return true;
    }

    private function add_post($ticket_id, $user_id, $type, $string){
        try{
            TicketPost::create([
                'ticket_id' => $ticket_id,
                'text' => $string,
                'ticketer_id' => $user_id,
                'ticketer_type' => $type
            ]);
        }
        catch(Throwable $e){
            Log::channel('throwable_db')->error($name.' update ticket db error '.$e->getMessage());
            return false;
        }
        $ticket = Ticket::find($ticket_id);
        if($type=='App\Models\User')
            $ticket->unread_by_school=1;
        else if($type=='App\Models\School')
            $ticket->unread_by_admin=1;
        $ticket->save();
        return true;
    }

    public function update(Ticket $ticket, Request $request){
        //check who wants to update the ticket
        if(Auth::user()){
            $user = Auth::user();
            $name = $user->username;
            $type = 'App\Models\User';
        }
        else if(Auth::guard('school')->user()){
            $user = Auth::guard('school')->user();
            $name = $user->name;
            $type = 'App\Models\School';
        }
        
        $validator = $this->validate_request($request);
        if($validator['error']==true){
            return back()->with('failure', $validator['message']);
        }

        DB::beginTransaction();
        try {
            if($ticket->solved){
                $open = $this->open_in_db($ticket);
                if(!$open){
                    throw new Exception('Failed to open ticket');
                }

                $new_post = $this->add_post($ticket->id, $user->id, $type, "Άνοιξε το δελτίο");
                if(!$new_post){
                    throw new Exception('Failed to add post');
                }
            }
            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            return back()->with('failure','Κάποιο σφάλμα προέκυψε, προσπαθήστε ξανά');
        }

        //check if there is a new post and add it
        if($request->input('comments')){
            $mails_comments = false;
            $sanitizedComments = strip_tags($request->input('comments'), '<p><a><b><i><u><ul><ol><li>'); //allow only these tags
            $new_post = $this->add_post($ticket->id, $user->id, $type, $sanitizedComments);
            if($new_post){
                $new_string = "\nΟ χρήστης ".$name." έγραψε:\n".$request->input('comments');
                $mails_comments = $this->send_update_mails($ticket, $new_string);
                Log::channel('tickets')->info($name." ticket $ticket->id updated comments");
            }  
        }  

        //check if there is a new attachment and add a post for it
        $error = false;
        if($request->hasFile('attachment')){
            $mails_upload = false;
            $file = $request->file('attachment');
            $directory = "tickets/$ticket->id";
            $fileHandler = new FilesController();
            $filename = $file->getClientOriginalName();
            $upload  = $fileHandler->upload_file($directory, $file, 'local');
            if($upload->getStatusCode() == 500){
                Log::channel('files')->error($name." ticket $ticket->id upload attachment error".$upload->getContent());
                return back()->with('failure', 'Δεν ήταν δυνατή η αποθήκευση του συνημμένου, προσπαθήστε ξανά');
            }
            else{
                Log::channel('tickets')->info($name." ticket $ticket->id uploaded file");
                $new_post = $this->add_post($ticket->id, $user->id, $type, "Προστέθηκε το αρχείο $filename");
                if($new_post){
                    $new_string = "\nΟ χρήστης ".$name." πρόσθεσε συνημμένο";  
                    $mails_upload = $this->send_update_mails($ticket, $new_string);
                }
            }
        }

        if((isset($mails_comments) and !$mails_comments) or (isset($mails_upload) and !$mails_upload))
            $mails = false;
        else
            $mails = true;
        
        if($mails)
            return back()->with('success', 'Το δελτίο ανανεώθηκε με την απάντησή σας'); 
        else
            return back()->with('warning', 'Το δελτίο ανανεώθηκε με την απάντησή σας, κάποια mail απέτυχαν να σταλούν'); 
    }

    public function mark_as_resolved(Ticket $ticket){
        if(Auth::user()){
            $user = Auth::user();
            $name = $user->username;
            $type='App\Models\User';
        }
        else if(Auth::guard('school')->user()){
            $user = Auth::guard('school')->user();
            $name = $user->name;
            $type='App\Models\School';
        }

        DB::beginTransaction();
        try {
            $resolve = $this->resolve_in_db($ticket);
            if(!$resolve){
                throw new Exception('Failed to resolve ticket');
            }

            $new_post = $this->add_post($ticket->id, $user->id, $type, "Έκλεισε το δελτίο");
            if(!$new_post){
                throw new Exception('Failed to add post');
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            return back()->with('failure','Κάποιο σφάλμα προέκυψε, προσπαθήστε ξανά');
        }
        Log::channel('tickets')->info($name." ticket $ticket->id resolved");

        return back()->with('success', 'Το δελτίο έκλεισε επιτυχώς');
    }

    public function ticket_needed_visit(Ticket $ticket, Request $request){
        if($request->input('checked')=='true')
            $ticket->needed_visit = 1;
        else
            $ticket->needed_visit = 0;
        $ticket->save();

        return response()->json(['message' => 'Ticket updated successfully']);
    }

    public function update_post(Request $request){
        $post = TicketPost::find($request->input('id'));
        $old_text = $post->text;
        if(!$post){
            return response()->json(['message' => 'Post not found'], 404);
        }

        // Get the currently logged in user or school
        $ticketer = null;
        $name = null;
        if (Auth::guard('web')->check()) {
            $ticketer = Auth::user();
            $name = $ticketer->username;
        } elseif (Auth::guard('school')->check()) {
            $ticketer = Auth::guard('school')->user();
            $name = $ticketer->name;
        }

        // Check if the logged in user or school is the ticketer of the post
        if (!$ticketer || $ticketer->getMorphClass() != $post->ticketer_type || $ticketer->id != $post->ticketer_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if($request->input('text')!=$old_text){
            $post->text = $request->input('text')."<p><em><small>Επεξεργασμένο</small></em></p>";
            $post->save();
            if($ticketer->getMorphClass()=='App\Models\User')
                $post->ticket->unread_by_school=1;
            else if($ticketer->getMorphClass()=='App\Models\School')
                $post->ticket->unread_by_admin=1;
            $post->ticket->save();
            if($post->ticket->solved){
                $open = $this->open_in_db($post->ticket);
                $new_post = $this->add_post($post->ticket->id, $ticketer->id, $ticketer->getMorphClass(), "Άνοιξε το δελτίο (επεξεργασία παλαιότερου σχολίου)");
                dd($new_post);
            }
            $id = $post->ticket->id;
            Log::channel('tickets')->info($name." ticket $id updated post");
            return response()->json(['message' => 'Post updated successfully']);
        }
        else{
            return response()->json(['message' => 'Post not changed']);
        }
    }

    public function download_file(Ticket $ticket, $original_filename){
        if(Auth::check()){
            $name = Auth::user()->username;
        }
        else if(Auth::guard('school')->check()){
            $name = Auth::guard('school')->user()->name;
        }
        $directory = "tickets/$ticket->id";
        $fileHandler = new FilesController();
        $download  = $fileHandler->download_file($directory, $original_filename, 'local');
        if($download->getStatusCode() == 500){
            Log::channel('files')->error($name." ticket $ticket->id download attachment error".$download->getContent());
            return back()->with('failure', 'Δεν ήταν δυνατή η λήψη του συνημμένου, προσπαθήστε ξανά');
        }
        return $download;
    }

    public function admin_create_ticket(Request $request){
        $school = School::where('name', $request->input('school'))->first();
        if(!$school){
            return back()->with(['failure' => 'Το σχολείο δεν βρέθηκε']);
        }
        $new_ticket = $this->create_db_entry($request->input('subject'), $request->input('comments'), $school->id);
        if($new_ticket!='error'){
            $mails = $this->send_creation_mails($new_ticket);
        }

        return redirect()->route('tickets.edit', ['ticket' => $new_ticket->id])->with('success','Το δελτίο δημιουργήθηκε με επιτυχία!');
    }

    public function microapp_create_ticket(Request $request, $appname){
        $school = Auth::guard('school')->user();
        $app = Microapp::where('url', '/'.$appname)->first()->name;
        if(!$app){
            return response()->json(['error' => 'Δεν βρέθηκε εφαρμογή με αυτό το url'], 404);
        }
        
        $new_ticket = $this->create_db_entry("Αυτόματο δελτίο από την εφαρμογή $app", $request->input('comments'));

        if($new_ticket!='error'){
            $mails = $this->send_creation_mails($new_ticket);
        }

        if($request->input('attachment')){
            $code = $school->code;
            $month = Month::getActiveMonth();
            $original_filename = $school->all_day_schools()->where('month_id', $month->id)->first()->file;
            Storage::makeDirectory("tickets/$new_ticket->id");
            $sourcePath = "all_day/all_day_".$code."_".$month->id.".xlsx";
            $destinationPath = "tickets/$new_ticket->id/$original_filename";
            try{
                $result = Storage::copy($sourcePath, $destinationPath);
            }
            catch(Throwable $e){
                return redirect()->route('tickets.edit', ['ticket' => $new_ticket->id])->with('warning','Το δελτίο δημιουργήθηκε με επιτυχία, αλλά δεν ήταν δυνατή η αποθήκευση του συνημμένου');
            }
            Log::channel('tickets')->info($school->name." ticket $new_ticket->id uploaded file");
            $new_post = $this->add_post($new_ticket->id, $school->id,'App\Models\School' , "Προστέθηκε το αρχείο $original_filename");
        }
        return redirect()->route('tickets.edit', ['ticket' => $new_ticket->id])->with('success','Το δελτίο δημιουργήθηκε με επιτυχία!');
    }
}
