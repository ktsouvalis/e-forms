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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TicketsController extends Controller
{
    //
    public function create_ticket(School $school, Request $request){
        try{
            $new_ticket = Ticket::create([
                'school_id' => Auth::guard('school')->user()->id,
                'subject' => $request->input('subject'),
                'comments' => Auth::guard('school')->user()->name.": ".$request->input('comments'),
                'solved' => 0
            ]); 
        }
        catch(Throwable $e){
            Log::channel('throwable_db')->error(Auth::guard('school')->user()->name." ticket creation failed");
            return redirect(url('/school_app/tickets'))->with('failure','Κάποιο σφάλμα προέκυψε, προσπαθήστε ξανά');
        }

        Log::channel('stakeholders_microapps')->info($new_ticket->school->name." ticket $new_ticket->id creation success");
        Log::channel('tickets')->info($new_ticket->school->name." ticket $new_ticket->id creation success");

        Mail::to("plinet_pe@dipe.ach.sch.gr")->send(new TicketCreated($new_ticket));
        Mail::to($new_ticket->school->mail)->send(new TicketCreated($new_ticket));
        foreach(Superadmin::all() as $superadmin){
            Mail::to($superadmin->user->email)->send(new TicketCreated($new_ticket));    
        }

        return redirect(url('/school_app/tickets'))->with('success','Το δελτίο δημιουργήθηκε με επιτυχία!');    
        
    }

    public function update_ticket(Ticket $ticket, Request $request){
        if(Auth::user()){
            $name = Auth::user()->username;
        }
        else if(Auth::guard('school')->user()){
            $name = Auth::guard('school')->user()->name;
        }
        $new_string = "\n".$name.": ".$request->input('comments');
        $updated_comments = $ticket->comments.$new_string;
        $ticket->comments = $updated_comments;
        $ticket->solved=0;
        $ticket->save();
        
        Mail::to($ticket->school->mail)->send(new TicketUpdated($ticket, $new_string, $ticket->school->name, $ticket->school->md5));
        Mail::to("plinet_pe@dipe.ach.sch.gr")->send(new TicketUpdated($ticket, $new_string, "Γραφείο Πλη.Νε.Τ.", ""));
        foreach(Superadmin::all() as $superadmin){
            Mail::to($superadmin->user->email)->send(new TicketUpdated($ticket, $new_string, $superadmin->user->username, ""));    
        }
        Log::channel('tickets')->info($name." ticket $ticket->id updated comments");
        return back()->with('success', 'Το δελτίο ανανεώθηκε με την απάντησή σας');
    }

    public function mark_as_resolved(Request $request, Ticket $ticket){
        $ticket->solved=1;
        if(Auth::user()){
            $name = Auth::user()->username;
        }
        else if(Auth::guard('school')->user()){
            $name = Auth::guard('school')->user()->name;
        }
        $new_string = "\nΟ χρήστης ".$name." έκλεισε το δελτίο";
        $updated_comments = $ticket->comments.$new_string;
        $ticket->comments = $updated_comments;
        $ticket->save();

        Log::channel('tickets')->info($name." ticket $ticket->id resolved");
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

        Log::channel('tickets')->info($name." ticket $ticket->id reopened");
        return back()->with('success', 'Το δελτίο άνοιξε ξανά και ειδοποιήθηκε η Τεχνική Υποστήριξη');
    }
}
