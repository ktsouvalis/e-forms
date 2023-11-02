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
        $url = $request->url();
        $segments = explode('/', $url);
        $app = end($segments);
        $microapp = Microapp::where('url', "/".$app)->firstOrFail(); 
        $user = Auth::guard('web')->user();
        if($user){
            if(Superadmin::where('user_id',$user->id)->exists()){
                return $next($request);
            }
            if(MicroappUser::where('user_id',$user->id)
                ->where('microapp_id', $microapp->id)
                ->exists()){
                    return $next($request);
            }
        }
        if($microapp->visible){
            $teacher = Auth::guard('teacher')->user();
            if($teacher){
                if(MicroappStakeholder::where('stakeholder_id',$teacher->id)
                    ->where('stakeholder_type', 'App\Models\Teacher')
                    ->where('microapp_id', $microapp->id)
                    ->exists()){
                        return $next($request);
                }
            }

            $school = Auth::guard('school')->user();
            if($school){
                if(MicroappStakeholder::where('stakeholder_id',$school->id)
                    ->where('microapp_id', $microapp->id)
                    ->where('stakeholder_type', 'App\Models\School')
                    ->exists()){
                        return $next($request);
                }
            }

            $consultant = Auth::guard('consultant')->user();
            if($consultant){
                if($microapp->url=="/internal_rules"){
                        return $next($request);
                }
            }
            abort(403, 'Unauthorized action.');
        }
        else{
            abort(403, 'Unauthorized action.');   
        }
    }
}
