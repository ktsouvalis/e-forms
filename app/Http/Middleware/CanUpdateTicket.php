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
            $ticket = $request->route('ticket'); // takes the {ticket} argument from the route, which is Ticket model
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
            abort(403, 'Unauthorized action.');
        }
        abort(403, 'Microapp not active');
    }
}
