<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class whocan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $formId = $request->route('form')->id;
        // echo $formId; exit;
        if(Auth::guard('teacher')->user()->forms()->where('form_id', $formId)->exists()){
        
            return $next($request);
        }
        return redirect(url('/index_teacher'))->with('failure', 'Δεν έχετε δικαίωμα πρόσβασης σε αυτόν τον πόρο');
    }
}
