<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanLoginAs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();
        if (!$user) {
            return redirect(url('/'))->with('warning', 'Πρέπει να είστε συνδεδεμένος για να κάνετε αυτή τη λειτουργία.');
        }
        //dd($user);
        return $next($request);
    }
}
