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

class TicketsController extends Controller
{
    //
    public function create_ticket(School $school, Request $request){
        $mail_failure=false;
        try{
            $new_ticket = Ticket::create([
                'school_id' => Auth::guard('school')->user()->id,
                'subject' => $request->input('subject'),
                'comments' => $request->input('comments'),
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
            $no_failure=true;
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
            return redirect(url('/school_app/tickets'))->with('success','Το δελτίο δημιουργήθηκε με επιτυχία!');  
        else
            return redirect(url('/school_app/tickets'))->with('warning','Το δελτίο δημιουργήθηκε με επιτυχία, κάποια mail απέτυχαν να σταλούν');       
        
    }

    public function update_ticket(Ticket $ticket, Request $request){
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
        $mail_failure=false;
        
        try{
            TicketPost::create([
                'ticket_id' => $ticket->id,
                'text' => $request->input('comments'),
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
        $ticket->solved=0;
        $ticket->save();
        $new_string = "\nΟ χρήστης ".$name." έγραψε:\n".$request->input('comments');
        try{
            Mail::to("plinet_pe@dipe.ach.sch.gr")->send(new TicketUpdated($ticket, $new_string, "Γραφείο Πλη.Νε.Τ.", ""));
            Mail::to("ktsouvalis@sch.gr")->send(new TicketUpdated($ticket, $new_string, "Γραφείο Πλη.Νε.Τ.", ""));
        }
        catch(Throwable $e){
            $no_failure=true;
            try{
                Log::channel('tickets')->info("update ticket ".$ticket->id." mail failure to plinet ".$e->getMessage());
            }
            catch(Throwable $e){
    
            }
        }

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
        // try{
            TicketPost::create([
                'ticket_id' => $ticket->id,
                'text' => $new_string,
                'ticketer_id' => $user->id,
                'ticketer_type' => $type
            ]);
        // }
        // catch(Throwable $e){
        //     try{
        //         Log::channel('throwable_db')->error($name.' update ticket db error '.$e->getMessage());
        //     }
        //     catch(Throwable $e){
    
        //     }
        //     return back()->with('failure','Κάποιο σφάλμα προέκυψε, προσπαθήστε ξανά');
        // }
       
        try{
            Log::channel('tickets')->info($name." ticket $ticket->id resolved");
        }
        catch(Throwable $e){
    
        }
        return back()->with('success', 'Το δελτίο έκλεισε επιτυχώς');
    }

    public function mark_as_open(Request $request, Ticket $ticket){
        $ticket->solved=0;
        if(Auth::user()){
            $name = Auth::user()->username;
        }
        else if(Auth::guard('school')->user()){
            $name = Auth::guard('school')->user()->name;
        }
        $new_string = "\nΟ χρήστης ".$name." άνοιξε το δελτίο";
        $updated_comments = $ticket->comments.$new_string;
        $ticket->comments = $updated_comments;
        $ticket->save();
        try{
            Log::channel('tickets')->info($name." ticket $ticket->id reopened");
        }
        catch(Throwable $e){
    
        }
        return back()->with('success', 'Το δελτίο άνοιξε ξανά και ειδοποιήθηκε η Τεχνική Υποστήριξη');
    }
}
