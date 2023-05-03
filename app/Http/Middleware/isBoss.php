<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
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
      
        foreach(User::where('id',Auth::id())->first()->roles as $one_role){
            if($one_role->role->name=='superuser'){
                return $next($request);
            }
        } 

        return redirect('/')->with('failure','Δεν έχετε δικαίωμα πρόσβασης σε αυτό τον πόρο');
    }
}
