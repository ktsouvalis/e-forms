<?php

namespace App\Http\Controllers\microapps;

use Throwable;
use App\Models\School;
use Illuminate\Http\Request;
use App\Models\microapps\Ticket;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TicketsController extends Controller
{
    //
    public function create_ticket(School $school, Request $request){
        try{
            Ticket::create([
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

        Log::channel('stakeholders_microapps')->info(Auth::guard('school')->user()->name." ticket creation success");
        Log::channel('tickets')->info(Auth::guard('school')->user()->name." ticket creation success");
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
        
        Log::channel('tickets')->info($name." ticket $ticket->id reopened");
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
