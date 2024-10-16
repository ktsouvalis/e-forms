<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Microapp;
use App\Models\Superadmin;
use App\Models\MicroappUser;
use Illuminate\Http\Request;
use App\Models\MicroappStakeholder;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanViewMicroapp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()->getName();
        $resource = explode('.', $routeName)[0];
        $microapp = Microapp::where('url', "/".$resource)->firstOrFail(); 
        if(!$microapp->active)
            abort(403, "Μη εξουσιοδοτημένη ενέργεια (inactive)");
        
        if(Auth::check()){
            $user = Auth::guard('web')->user();
            if($user->isAdmin()){
                return $next($request);
            }
            if($user->microapps->where('microapp_id', $microapp->id)->count()){
                return $next($request);
            }
            abort(403, "Μη εξουσιοδοτημένη ενέργεια (no access)");
        }

        if(!$microapp->visible)
            abort(403, "Μη εξουσιοδοτημένη ενέργεια (invisible)");

        if(Auth::guard('teacher')->check()){
            $teacher = Auth::guard('teacher')->user();
            if($teacher->microapps->where('microapp_id', $microapp->id)->count()){
                return $next($request);
            }
            else { 
                if($microapp->accessCriteria){
                    $criteria = json_decode($microapp->accessCriteria->criteria, true);
                    foreach ($criteria as $key => $value) {
                        if (!in_array($teacher->$key, $value)) {
                            abort(403, "Μη εξουσιοδοτημένη ενέργεια (no access)");
                        }
                    }
                    return $next($request);
                }
            }
        }

        if(Auth::guard('school')->check()){
            $school = Auth::guard('school')->user();
            if($school->microapps->where('microapp_id', $microapp->id)->count()){
                return $next($request);
            }
        }

        if(Auth::guard('consultant')->check()){
            $consultant = Auth::guard('consultant')->user();
            if($microapp->url=="/internal_rules" or $microapp->url=="/work_planning"){
                return $next($request);
            }
        }

        abort(403, "Μη εξουσιοδοτημένη ενέργεια (no access)");      
    }
}
