<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Superadmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isBoss
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Superadmin::where('user_id',Auth::guard('web')->id())->exists()){
            return $next($request);
        } 

        return redirect(url('/index_user'))->with('failure','Δεν έχετε δικαίωμα πρόσβασης σε αυτό τον πόρο');
    }
}
