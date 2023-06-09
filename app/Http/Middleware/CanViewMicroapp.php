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

        if(MicroappUser::where('user_id',Auth::id())
            ->where('microapp_id', $microapp->id)
            ->exists()){
                return $next($request);
        }

        if(MicroappStakeholder::where('stakeholder_id',Auth::guard('teacher')->id())
            ->where('stakeholder_type', 'App\Models\Teacher')
            ->where('microapp_id', $microapp->id)
            ->exists()){
                return $next($request);
        }

        if(MicroappStakeholder::where('stakeholder_id',Auth::guard('school')->id())
            ->where('microapp_id', $microapp->id)
            ->where('stakeholder_type', 'App\Models\School')
            ->exists()){
                return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
