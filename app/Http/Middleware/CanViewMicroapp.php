<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Microapp;
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
        $microapp = Microapp::where('url', "/".$app)->first();
        
        $user = Auth::guard('web')->user();
        if($user){
            // echo 'web'; exit;
            if(MicroappUser::where('user_id',$user->id)
                ->where('microapp_id', $microapp->id)
                ->exists()){
                    return $next($request);
            }
        }

        $teacher = Auth::guard('teacher')->user();
        if($teacher){
            // echo 'teacher'; exit;
            if(MicroappStakeholder::where('stakeholder_id',$teacher->id)
                ->where('stakeholder_type', 'App\Models\Teacher')
                ->where('microapp_id', $microapp->id)
                ->exists()){
                    return $next($request);
            }
        }

        $school = Auth::guard('school')->user();
        if($school){
            // echo 'school'; exit;
            if(MicroappStakeholder::where('stakeholder_id',$school->id)
                ->where('microapp_id', $microapp->id)
                ->where('stakeholder_type', 'App\Models\School')
                ->exists()){
                    return $next($request);
            }
        }
        
        abort(403, 'Unauthorized action.');
    }
}
