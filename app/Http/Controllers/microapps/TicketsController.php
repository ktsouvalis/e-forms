<?php

namespace App\Http\Controllers\microapps;

use Throwable;
use App\Models\School;
use App\Models\Superadmin;
use App\Mail\TicketCreated;
use App\Mail\TicketUpdated;
use Illuminate\Http\Request;
use App\Models\microapps\Ticket;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\microapps\TicketPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Validator;

class TicketsController extends Controller
{
    //
    public function create_ticket(Request $request){
        $school = Auth::guard('school')->user();
        $mail_failure=false;

        $validator = Validator::make($request->all(), [
            'comments' => 'required|string|max:5000', // Adjust the max length as needed
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Sanitize the input (optional)
        $sanitizedComments = strip_tags($request->input('comments'), '<p><a><b><i><u><ul><ol><li>'); //allow only these tags
        try{
            $new_ticket = Ticket::create([
                'school_id' => Auth::guard('school')->user()->id,
                'subject' => $request->input('subject'),
                'comments' => $sanitizedComments,
                'solved' => 0
            ]); 
        }
        catch(Throwable $e){
            try{
                Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create ticket db error '.$e->getMessage());
            }
            catch(Throwable $e){
    
            }
            return redirect(url('/school_app/tickets'))->with('failure','Κάποιο σφάλμα προέκυψε, προσπαθήστε ξανά');
        }
        try{
            Log::channel('stakeholders_microapps')->info($new_ticket->school->name." ticket $new_ticket->id creation success");
        }
        catch(Throwable $e){
    
        }
        try{
            Log::channel('tickets')->info($new_ticket->school->name." ticket $new_ticket->id creation success");
        }
        catch(Throwable $e){
    
        }

        try{
            Mail::to("plinet_pe@dipe.ach.sch.gr")->send(new TicketCreated($new_ticket));
            Mail::to("ktsouvalis@sch.gr")->send(new TicketCreated($new_ticket));
        }
        catch(Throwable $e){
            try{
                Log::channel('tickets')->info("new ticket ".$new_ticket->id." mail failure to plinet ".$e->getMessage());
            }
            catch(Throwable $e){
    
            }
        }

        try{
            Mail::to($new_ticket->school->mail)->send(new TicketCreated($new_ticket));
        }
        catch(Throwable $e){
            $mail_failure=true;
            try{  
                Log::channel('tickets')->info("new ticket ".$new_ticket->id." mail failure to school ".$e->getMessage()); 
            }
            catch(Throwable $e){
    
            }
        }

        if(!$mail_failure)
            return redirect(url("/ticket_profile/$new_ticket->id"))->with('success','Το δελτίο δημιουργήθηκε με επιτυχία!');  
        else
            return redirect(url("/ticket_profile/$new_ticket->id"))->with('warning','Το δελτίο δημιουργήθηκε με επιτυχία, κάποια mail απέτυχαν να σταλούν');       
        
    }

    public function update_ticket(Ticket $ticket, Request $request){
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
        

        // Validate the request
        $validator = Validator::make($request->all(), [
            'comments' => 'required_without:attachment|max:5000',
            'attachment' => 'required_without:comments|file|max:10240|mimes:xlsx,jpg,png,pdf,docx' 
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return back()
                ->with('failure',$validator->errors()->first());
        }

        //if validation passes, mark the ticket as not solved and add a post
        if($ticket->solved){
            $ticket->solved=0;
            $ticket->save();
            TicketPost::create([
                'ticket_id' => $ticket->id,
                'text' => "Ο χρήστης $name άνοιξε το δελτίο",
                'ticketer_id' => $user->id,
                'ticketer_type' => $type
            ]);
        }

        //check if there is a new post and add it
        if($request->input('comments')){
            $sanitizedComments = strip_tags($request->input('comments'), '<p><a><b><i><u><ul><ol><li>'); //allow only these tags
            try{
                TicketPost::create([
                    'ticket_id' => $ticket->id,
                    'text' => $sanitizedComments,
                    'ticketer_id' => $user->id,
                    'ticketer_type' => $type
                ]);
            }
            catch(Throwable $e){
                try{
                    Log::channel('throwable_db')->error($name.' update ticket db error '.$e->getMessage());
                }
                catch(Throwable $e){
        
                }
                return back()->with('failure','Κάποιο σφάλμα προέκυψε, προσπαθήστε ξανά');
            }
            $new_string = "\nΟ χρήστης ".$name." έγραψε:\n".$request->input('comments');
        }  

        //check if there is a new attachment and add a post for it
        $error = false;
        if($request->hasFile('attachment')){
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
                TicketPost::create([
                    'ticket_id' => $ticket->id,
                    'text' => "Ο χρήστης $name πρόσθεσε το αρχείο $filename",
                    'ticketer_id' => $user->id,
                    'ticketer_type' => $type
                ]);
                $new_string = "\nΟ χρήστης ".$name." πρόσθεσε συνημμένο";  
            }
        }

        //send mails to us
        $mail_failure=false;
        try{
            Mail::to("plinet_pe@dipe.ach.sch.gr")->send(new TicketUpdated($ticket, $new_string, "Γραφείο Πλη.Νε.Τ.", ""));
            Mail::to("ktsouvalis@sch.gr")->send(new TicketUpdated($ticket, $new_string, "Γραφείο Πλη.Νε.Τ.", ""));
        }
        catch(Throwable $e){
            $mail_failure=true;
            try{
                Log::channel('tickets')->info("update ticket ".$ticket->id." mail failure to plinet ".$e->getMessage());
            }
            catch(Throwable $e){
    
            }
        }

        //send mail to school
        try{
            Mail::to($ticket->school->mail)->send(new TicketUpdated($ticket, $new_string, $ticket->school->name, $ticket->school->md5));
        }
        catch(Throwable $e){
            $mail_failure=true;  
            try{
                Log::channel('tickets')->info("update ticket ".$ticket->id." mail failure to school ".$e->getMessage());
            }
            catch(Throwable $e){
    
            } 
        }

        Log::channel('tickets')->info($name." ticket $ticket->id updated comments");
        if(!$mail_failure)
            return back()->with('success', 'Το δελτίο ανανεώθηκε με την απάντησή σας'); 
        else
            return back()->with('warning', 'Το δελτίο ανανεώθηκε με την απάντησή σας, κάποια mail απέτυχαν να σταλούν'); 
    }

    public function mark_as_resolved(Request $request, Ticket $ticket){
        $ticket->solved=1;
        $ticket->save();
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
        $new_string = "Ο χρήστης ".$name." έκλεισε το δελτίο";
        try{
            TicketPost::create([
                'ticket_id' => $ticket->id,
                'text' => $new_string,
                'ticketer_id' => $user->id,
                'ticketer_type' => $type
            ]);
        }
        catch(Throwable $e){
            try{
                Log::channel('throwable_db')->error($name.' update ticket db error '.$e->getMessage());
            }
            catch(Throwable $e){
    
            }
            return back()->with('failure','Κάποιο σφάλμα προέκυψε, προσπαθήστε ξανά');
        }
       
        try{
            Log::channel('tickets')->info($name." ticket $ticket->id resolved");
        }
        catch(Throwable $e){
    
        }
        return back()->with('success', 'Το δελτίο έκλεισε επιτυχώς');
    }

    public function ticket_needed_visit(Ticket $ticket, Request $request){
        if($request->input('checked')=='true')
            $ticket->needed_visit = 1;
        else
            $ticket->needed_visit = 0;
        $ticket->save();

        return response()->json(['message' => 'Fileshare updated successfully']);
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
            if($post->ticket->solved){
                $post->ticket->solved=0;
                $post->ticket->save();
                TicketPost::create([
                    'ticket_id' => $post->ticket->id,
                    'text' => "Ο χρήστης $name άνοιξε το δελτίο (επεξεργασία παλαιότερου σχολίου)",
                    'ticketer_id' => $ticketer->id,
                    'ticketer_type' => $ticketer->getMorphClass()
                ]);
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
}
