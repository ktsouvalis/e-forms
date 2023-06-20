<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanDownloadFiles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();
        if($user){
            return $next($request);   
        }
            
        $teacher = Auth::guard('teacher')->user();
        if($teacher){
            return $next($request);
        }

        $school = Auth::guard('school')->user();
        if($school){
            return $next($request);
        }
    
        abort(403);
    }
}
