<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\School;
use App\Models\Microapp;
use App\Models\MicroappUser;
use Illuminate\Http\Request;
use App\Models\microapps\SchoolArea;
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
        $routeName = $request->route()->getName();
        $school = School::find($request->route('school_area')); //it may be {school_area} but it is actually the school_id
        $resource = explode('.', $routeName)[0];
        $microapp = Microapp::where('url', "/".$resource)->firstOrFail(); 
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
            abort(403, 'Εφαρμογή "Όρια Σχολείων" μη ενεργή'); 
        }
        elseif(Auth::guard('school')->check()){
            if($microapp->active and $microapp->visible){
                $loggedinSchool = Auth::guard('school')->user();
                if($school->id == $loggedinSchool->id)
                    return $next($request);
                else
                    abort(403, 'Μη εξουσιοδοτημένη ενέργεια.');
            } 
            abort(403, 'Εφαρμογή "Όρια Σχολείων" μη ενεργή');
        }
        abort(403, 'Μη εξουσιοδοτημένη ενέργεια');
    }
}

