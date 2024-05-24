<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Microapp;
use App\Models\MicroappUser;
use Illuminate\Http\Request;
use App\Models\microapps\Ticket;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanUpdateTicket
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $microapp = Microapp::where('url','/tickets')->first();
        if($microapp->active and $microapp->visible and $microapp->accepts){
            $ticketParam = $request->route('ticket');
            //If you're making an AJAX request and passing the ticket ID in the URL, 
            // Laravel's route model binding won't automatically inject the Ticket model instance. 
            // In this case, you do need to manually find the Ticket model using Ticket::find().
            $ticket = $ticketParam instanceof Ticket ? $ticketParam : Ticket::find($ticketParam);
            if(Auth::check()){
                $user = Auth::guard('web')->user();
                if($user->isAdmin()){
                    return $next($request);
                }
                if(MicroappUser::where('user_id',$user->id)
                    ->where('microapp_id', Microapp::where('url','/tickets')->first()->id)
                    ->exists()){
                        return $next($request);
                }
            }
            
            if(Auth::guard('school')->check()){
                $school = Auth::guard('school')->user();
                if($ticket->school_id == $school->id)
                return $next($request);
            }
            abort(403, 'Μη εξουσιοδοτημένη ενέργεια');
        }
        abort(403, 'Εφαρμογή "Τεχνική Στήριξη" μη ενεργή');
    }
}
