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
        $user = User::where('id',Auth::id())->first();
        if($user->id == 1 or $user->id == 2){
            return $next($request);
        } 

        return redirect(url('/'))->with('failure','Δεν έχετε δικαίωμα πρόσβασης σε αυτό τον πόρο');
    }
}
