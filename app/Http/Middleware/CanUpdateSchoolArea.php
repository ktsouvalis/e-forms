<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Microapp;
use App\Models\MicroappUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanUpdateSchoolArea
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $school = $request->route('school');
        $microapp = Microapp::where('url','/school_area')->first();
        if(Auth::check()){
            $user = Auth::guard('web')->user();
            if($microapp->active){
                if($user->isAdmin()){
                    return $next($request);
                }
                if(MicroappUser::where('user_id',$user->id)
                    ->where('microapp_id', $microapp->id)
                    ->where('can_edit', 1)
                    ->exists()){
                        return $next($request);
                }
            }
            abort(403, 'Microapp not active'); 
        }
        elseif(Auth::guard('school')->check()){
            if($microapp->active and $microapp->visible){
                $loggedinSchool = Auth::guard('school')->user();
                if($school->id == $loggedinSchool->id)
                    return $next($request);
                else
                    abort(403, 'Unauthorized action.');
            } 
            abort(403, 'Microapp not active');
        }
        abort(403, 'Unauthorized action.');
    }
}

